<?php

/**
 * CloudArcade CMS System Updater
 * Handles secure system updates with validation and rollback capabilities
 */

class SystemUpdater
{
    private $apiEndpoint = 'https://api.cloudarcade.net/cms-update/download.php';
    private $tempDir;
    private $purchaseCode;
    private $currentVersion;
    private $targetVersion;
    private $updateToken;
    private $logFilePath; // New property for log file path

    private $lastError = null;
    private $isRollbackNeeded = false;

    public function __construct()
    {
        $this->tempDir = ABSPATH . 'content/temp';
        $this->purchaseCode = check_purchase_code();
        $this->currentVersion = VERSION;
        $this->targetVersion = implode('.', str_split(str_pad(((int)str_replace('.', '', VERSION)) + 1, 3, '0', STR_PAD_LEFT)));
        
        // Set the log file path (one directory up, in admin folder)
        $this->logFilePath = dirname(__FILE__) . '/../admin/last-update-log.txt';
    }

    /**
     * Initialize or reset the log file
     */
    private function initLogFile()
    {
        // If log file exists, delete it to start fresh
        if (file_exists($this->logFilePath)) {
            unlink($this->logFilePath);
        }
        
        // Start with log header
        $timestamp = date('Y-m-d H:i:s');
        $this->writeToLog("CloudArcade CMS Update Log - Started at $timestamp");
        $this->writeToLog("Current Version: " . $this->currentVersion);
        $this->writeToLog("Target Version: " . $this->targetVersion);
        $this->writeToLog("---------------------------------------------------");
    }
    
    /**
     * Write a message to the log file
     */
    private function writeToLog($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] $message" . PHP_EOL;
        
        file_put_contents($this->logFilePath, $formattedMessage, FILE_APPEND);
    }

    /**
     * Check if a system update is available
     */
    public function checkUpdate()
    {
        try {
            // Verify purchase code is valid
            if (!$this->purchaseCode) {
                throw new Exception('Invalid purchase code');
            }

            // Make API request to check for updates
            $params = [
                'action' => 'check',
                'code' => $this->purchaseCode,
                'current_version' => $this->currentVersion
            ];

            if (isset($_GET['test_update'])) {
                $params['test'] = true;
            }

            $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

            if (!$response || !isset($response['status'])) {
                throw new Exception('Invalid response from update server');
            }

            // Format the response
            switch ($response['status']) {
                case 'current':
                    return [
                        'status' => 'current',
                        'current_version' => $response['version'],
                        'message' => 'System is up to date'
                    ];

                case 'update':
                    return [
                        'status' => 'update',
                        'current_version' => $response['current'],
                        'next_version' => $response['next'],
                        'latest_version' => $response['latest'],
                        'changes' => isset($response['info']['changes']) ? $response['info']['changes'] : null,
                        'message' => 'Update available'
                    ];

                default:
                    throw new Exception($response['message'] ?? 'Unknown error occurred');
            }
        } catch (Exception $e) {
            $this->logError('Update check failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Main update method that orchestrates the entire update process
     */
    public function performUpdate()
    {
        // Initialize log file at the start of update process
        $this->initLogFile();
        $this->writeToLog("Starting update process from {$this->currentVersion} to {$this->targetVersion}");
        
        // Update is always increcement
        try {
            // Clean previous temp files if any
            $this->cleanTempFiles();

            // Ensure temp directory exists
            if (!file_exists($this->tempDir)) {
                mkdir($this->tempDir, 0755, true);
                $this->writeToLog("Created temporary directory");
            }

            if (!$this->requestDownloadToken()) {
                throw new Exception('Failed to obtain download token');
            }

            // Download update package
            $this->writeToLog("Downloading update package...");
            $downloadPath = $this->downloadUpdate();
            if (!$downloadPath) {
                throw new Exception('Failed to download update package');
            }
            $this->writeToLog("Update package downloaded successfully");

            // Create backup using existing method
            $this->writeToLog("Creating system backup...");
            if (!$this->createBackup()) {
                throw new Exception('Failed to create backup');
            }
            $this->writeToLog("System backup created successfully");

            // Verify and extract update
            $this->writeToLog("Verifying and extracting update package...");
            if (!$this->verifyAndExtractUpdate($downloadPath)) {
                throw new Exception('Failed to verify or extract update package');
            }
            $this->writeToLog("Update package verified and extracted successfully");

            // Install update
            $this->isRollbackNeeded = true; // Set flag before making changes
            $this->writeToLog("Installing update...");
            if (!$this->installUpdate()) {
                throw new Exception('Failed to install update');
            }
            $this->writeToLog("Update installed successfully");

            // Cleanup
            $this->cleanTempFiles();
            $this->writeToLog("Cleaned up temporary files");
            $this->isRollbackNeeded = false;

            $this->writeToLog("Update process completed successfully");
            $this->writeToLog("System updated from {$this->currentVersion} to {$this->targetVersion}");
            
            return [
                'status' => 'success',
                'message' => 'Update completed successfully'
            ];
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logError($e->getMessage());
            $this->writeToLog("ERROR: " . $e->getMessage());

            if ($this->isRollbackNeeded) {
                $this->writeToLog("Rollback needed but not implemented yet");
                //
            }

            // Cleanup
            $this->cleanTempFiles();
            $this->writeToLog("Cleaned temporary files after error");
            $this->writeToLog("Update process failed");

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Request a download token from the API
     */
    private function requestDownloadToken()
    {
        $params = [
            'code' => $this->purchaseCode,
            'version' => $this->targetVersion,
            'generate' => 1,
            'ref' => DOMAIN
        ];

        if (isset($_GET['test_update'])) {
            $params['test'] = true;
        }

        $response = $this->makeApiRequest($this->apiEndpoint, $params);
        if (!$response || !isset($response['status']) || $response['status'] !== 'success') {
            $this->writeToLog("Failed to get download token: " . json_encode($response));
            return false;
        }

        $this->updateToken = $response['token'];
        return true;
    }

    /**
     * Check and perform database updates if available
     */
    private function handleDatabaseUpdate()
    {
        $this->writeToLog("Checking for database updates...");
        
        $params = [
            'action' => 'db',
            'version' => $this->targetVersion
        ];

        if (isset($_GET['test_update'])) {
            $params['test'] = true;
        }

        // Get DB update info from API
        $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

        if (!$response || !isset($response['status'])) {
            $this->writeToLog("Failed to check for database updates");
            throw new Exception('Failed to check for database updates');
        }

        // If no DB updates found, return true
        if ($response['status'] !== 'found') {
            $this->writeToLog("Database update NOT found");
            return true;
        }

        $dbUpdates = $response['db'];
        $conn = open_connection();

        $this->writeToLog("Database update started");

        try {
            // Handle indexes
            if (isset($dbUpdates['indexes'])) {
                $this->writeToLog("Processing database indexes...");
                foreach ($dbUpdates['indexes'] as $tableName => $indexes) {
                    // Verify table exists before attempting index operations
                    if (!$conn->query("SHOW TABLES LIKE '$tableName'")->rowCount()) {
                        $this->writeToLog("Table '$tableName' does not exist, skipping index operations");
                        continue; // Skip if table doesn't exist
                    }

                    foreach ($indexes as $indexName => $indexInfo) {
                        // Check if index exists
                        $checkIndexSql = "SHOW INDEX FROM `$tableName` WHERE Key_name = '$indexName'";
                        $indexExists = $conn->query($checkIndexSql)->rowCount() > 0;

                        switch ($indexInfo['action']) {
                            case 'add_if_not_exists':
                                if (!$indexExists) {
                                    $this->writeToLog("Adding index '$indexName' to table '$tableName'");
                                    $stmt = $conn->prepare($indexInfo['sql']);
                                    if (!$stmt->execute()) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to add index '$indexName': " . $errorInfo[2]);
                                        throw new Exception("Failed to add index $indexName to table $tableName: " . $errorInfo[2]);
                                    }

                                    // Verify index was created
                                    if ($conn->query($checkIndexSql)->rowCount() == 0) {
                                        $this->writeToLog("ERROR: Index '$indexName' creation verification failed");
                                        throw new Exception("Index $indexName creation verification failed");
                                    }
                                    $this->writeToLog("Index '$indexName' added successfully");
                                } else {
                                    $this->writeToLog("Index '$indexName' already exists, skipping");
                                }
                                break;
                            case 'drop_if_exists':
                                if ($indexExists) {
                                    $this->writeToLog("Dropping index '$indexName' from table '$tableName'");
                                    $dropSql = "DROP INDEX `$indexName` ON `$tableName`";
                                    $stmt = $conn->prepare($dropSql);
                                    if (!$stmt->execute()) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to drop index '$indexName': " . $errorInfo[2]);
                                        throw new Exception("Failed to drop index $indexName from table $tableName: " . $errorInfo[2]);
                                    }
                                    $this->writeToLog("Index '$indexName' dropped successfully");
                                } else {
                                    $this->writeToLog("Index '$indexName' does not exist, skipping drop operation");
                                }
                                break;
                        }
                    }
                }
            }

            // Handle table operations
            if (isset($dbUpdates['tables'])) {
                $this->writeToLog("Processing database tables...");
                foreach ($dbUpdates['tables'] as $tableName => $tableInfo) {
                    // Check if table exists
                    $tableExists = $conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

                    switch ($tableInfo['action']) {
                        case 'create_if_not_exists':
                            if (!$tableExists) {
                                $this->writeToLog("Creating table '$tableName'");
                                $stmt = $conn->prepare($tableInfo['sql']);
                                if (!$stmt->execute()) {
                                    $errorInfo = $stmt->errorInfo();
                                    $this->writeToLog("ERROR: Failed to create table '$tableName': " . $errorInfo[2]);
                                    throw new Exception("Failed to create table $tableName: " . $errorInfo[2]);
                                }

                                // Verify table was created
                                if ($conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() == 0) {
                                    $this->writeToLog("ERROR: Table '$tableName' creation verification failed");
                                    throw new Exception("Table $tableName creation verification failed");
                                }
                                $this->writeToLog("Table '$tableName' created successfully");
                            } else {
                                $this->writeToLog("Table '$tableName' already exists, skipping creation");
                            }
                            break;
                        case 'drop_if_exists':
                            if ($tableExists) {
                                $this->writeToLog("Dropping table '$tableName'");
                                $stmt = $conn->prepare("DROP TABLE `$tableName`");
                                if (!$stmt->execute()) {
                                    $errorInfo = $stmt->errorInfo();
                                    $this->writeToLog("ERROR: Failed to drop table '$tableName': " . $errorInfo[2]);
                                    throw new Exception("Failed to drop table $tableName: " . $errorInfo[2]);
                                }
                                $this->writeToLog("Table '$tableName' dropped successfully");
                            } else {
                                $this->writeToLog("Table '$tableName' does not exist, skipping drop operation");
                            }
                            break;
                    }
                }
            }

            // Handle column operations with improved error handling
            if (isset($dbUpdates['columns'])) {
                $this->writeToLog("Processing database columns...");
                foreach ($dbUpdates['columns'] as $tableName => $columns) {
                    // Verify table exists before attempting column operations
                    if (!$conn->query("SHOW TABLES LIKE '$tableName'")->rowCount()) {
                        $this->writeToLog("Table '$tableName' does not exist, skipping column operations");
                        continue; // Skip if table doesn't exist
                    }

                    foreach ($columns as $columnName => $columnInfo) {
                        $columnExists = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'")->rowCount() > 0;

                        switch ($columnInfo['action']) {
                            case 'add_if_not_exists':
                                if (!$columnExists) {
                                    $this->writeToLog("Adding column '$columnName' to table '$tableName'");
                                    $stmt = $conn->prepare($columnInfo['sql']);
                                    if (!$stmt->execute()) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to add column '$columnName': " . $errorInfo[2]);
                                        throw new Exception("Failed to add column $columnName to table $tableName: " . $errorInfo[2]);
                                    }

                                    // Verify column was added
                                    if ($conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'")->rowCount() == 0) {
                                        $this->writeToLog("ERROR: Column '$columnName' creation verification failed");
                                        throw new Exception("Column $columnName creation verification failed");
                                    }
                                    $this->writeToLog("Column '$columnName' added successfully");
                                } else {
                                    $this->writeToLog("Column '$columnName' already exists, skipping");
                                }
                                break;
                            case 'modify':
                                if ($columnExists) {
                                    $this->writeToLog("Modifying column '$columnName' in table '$tableName'");
                                    $stmt = $conn->prepare($columnInfo['sql']);
                                    if (!$stmt->execute()) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to modify column '$columnName': " . $errorInfo[2]);
                                        throw new Exception("Failed to modify column $columnName in table $tableName: " . $errorInfo[2]);
                                    }
                                    $this->writeToLog("Column '$columnName' modified successfully");
                                } else {
                                    $this->writeToLog("Column '$columnName' does not exist, skipping modification");
                                }
                                break;
                        }
                    }
                }
            }

            // Handle data operations with improved error handling
            if (isset($dbUpdates['data'])) {
                $this->writeToLog("Processing database data operations...");
                foreach ($dbUpdates['data'] as $tableName => $operations) {
                    // Check if table exists first
                    if (!$conn->query("SHOW TABLES LIKE '$tableName'")->rowCount()) {
                        $this->writeToLog("Table '$tableName' does not exist, skipping data operations");
                        continue; // Skip if table doesn't exist
                    }

                    foreach ($operations as $operation) {
                        try {
                            switch ($operation['action']) {
                                case 'insert':
                                    // Regular insert
                                    $this->writeToLog("Inserting data into table '$tableName'");
                                    $stmt = $conn->prepare($operation['sql']);
                                    if (!$stmt->execute($operation['params'] ?? [])) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to insert data: " . $errorInfo[2]);
                                        throw new Exception("Failed to insert data into $tableName: " . $errorInfo[2]);
                                    }
                                    $this->writeToLog("Data inserted successfully");
                                    break;

                                case 'insert_if_not_exists':
                                    // Check if data exists using the provided check condition
                                    $this->writeToLog("Checking if data exists in table '$tableName'");
                                    if (isset($operation['check_sql'])) {
                                        $checkStmt = $conn->prepare($operation['check_sql']);
                                        $checkStmt->execute($operation['check_params'] ?? []);

                                        if ($checkStmt->rowCount() === 0) {
                                            // Data doesn't exist, perform insert
                                            $this->writeToLog("Data does not exist, performing insert");
                                            $stmt = $conn->prepare($operation['sql']);
                                            if (!$stmt->execute($operation['params'] ?? [])) {
                                                $errorInfo = $stmt->errorInfo();
                                                $this->writeToLog("ERROR: Failed to insert data: " . $errorInfo[2]);
                                                throw new Exception("Failed to insert data into $tableName: " . $errorInfo[2]);
                                            }

                                            // Verify the insertion
                                            $this->writeToLog("Verifying data insertion");
                                            $verifyStmt = $conn->prepare($operation['check_sql']);
                                            $verifyStmt->execute($operation['check_params'] ?? []);
                                            if ($verifyStmt->rowCount() === 0) {
                                                $this->writeToLog("ERROR: Failed to verify data insertion");
                                                throw new Exception("Failed to verify data insertion in $tableName");
                                            }
                                            $this->writeToLog("Data inserted and verified successfully");
                                        } else {
                                            $this->writeToLog("Data already exists, skipping insertion");
                                        }
                                    } else {
                                        $this->writeToLog("ERROR: Missing check_sql for insert_if_not_exists");
                                        throw new Exception("Missing check_sql for insert_if_not_exists in $tableName");
                                    }
                                    break;

                                case 'update':
                                    $this->writeToLog("Updating data in table '$tableName'");
                                    $stmt = $conn->prepare($operation['sql']);
                                    if (!$stmt->execute($operation['params'] ?? [])) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to update data: " . $errorInfo[2]);
                                        throw new Exception("Failed to update data in $tableName: " . $errorInfo[2]);
                                    }
                                    $this->writeToLog("Data updated successfully");
                                    break;

                                case 'delete':
                                    $this->writeToLog("Deleting data from table '$tableName'");
                                    $stmt = $conn->prepare($operation['sql']);
                                    if (!$stmt->execute($operation['params'] ?? [])) {
                                        $errorInfo = $stmt->errorInfo();
                                        $this->writeToLog("ERROR: Failed to delete data: " . $errorInfo[2]);
                                        throw new Exception("Failed to delete data from $tableName: " . $errorInfo[2]);
                                    }
                                    $this->writeToLog("Data deleted successfully");
                                    break;
                            }
                        } catch (PDOException $e) {
                            // Handle specific database errors
                            if (($operation['action'] === 'insert' || $operation['action'] === 'insert_if_not_exists')
                                && $e->getCode() == '23000'
                            ) {
                                // Duplicate key error - might be expected, log and continue
                                $this->writeToLog("NOTICE: Duplicate key error in '$tableName': " . $e->getMessage());
                                $this->logError("Duplicate key error in $tableName: " . $e->getMessage());
                                continue;
                            }
                            // Re-throw other exceptions
                            $this->writeToLog("ERROR: Database exception: " . $e->getMessage());
                            throw $e;
                        }
                    }
                }
            }

            $this->writeToLog("Database updated successfully");
            return true;
        } catch (Exception $e) {
            $this->writeToLog("ERROR: Database update failed: " . $e->getMessage());
            $this->logError('Database update failed: ' . $e->getMessage());
            throw new Exception('Database update failed: ' . $e->getMessage());
        }
    }

    /**
     * Download the update package using the token
     */
    private function downloadUpdate()
    {
        if (!$this->updateToken) {
            $this->writeToLog("ERROR: No update token available for download");
            return false;
        }

        $downloadPath = $this->tempDir . '/update.zip';

        $params = [
            'token' => $this->updateToken,
            'v' => VERSION,
            'ref' => DOMAIN
        ];

        if (isset($_GET['test_update'])) {
            $params['test'] = true;
        }

        return $this->downloadFile($this->apiEndpoint, $params, $downloadPath);
    }

    /**
     * Create system backup before update using existing backup_cms function
     */
    private function createBackup()
    {
        try {
            // Using existing backup function
            backup_cms(ABSPATH, 'part');
            $this->writeToLog("System backup created successfully");
            return true;
        } catch (Exception $e) {
            $this->writeToLog("ERROR: Backup failed: " . $e->getMessage());
            $this->logError('Backup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify and extract update package
     */
    private function verifyAndExtractUpdate($packagePath)
    {
        // Handle database updates first
        try {
            $this->handleDatabaseUpdate();
        } catch (Exception $e) {
            $this->writeToLog("ERROR: Database update failed: " . $e->getMessage());
            throw new Exception('Database update failed, update process aborted: ' . $e->getMessage());
        }

        $zip = new ZipArchive();
        $zipResult = $zip->open($packagePath);

        if ($zipResult !== true) {
            $errorMessages = [
                ZipArchive::ER_EXISTS => 'File already exists',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                ZipArchive::ER_INVAL => 'Invalid argument',
                ZipArchive::ER_MEMORY => 'Memory allocation failure',
                ZipArchive::ER_NOENT => 'No such file',
                ZipArchive::ER_NOZIP => 'Not a zip archive',
                ZipArchive::ER_OPEN => 'Can\'t open file',
                ZipArchive::ER_READ => 'Read error',
                ZipArchive::ER_SEEK => 'Seek error'
            ];

            $errorMessage = isset($errorMessages[$zipResult])
                ? $errorMessages[$zipResult]
                : 'Unknown ZIP error';

            $this->writeToLog("ERROR: Failed to open update package: " . $errorMessage);
            throw new Exception('Failed to open update package: ' . $errorMessage);
        }

        $this->writeToLog("Update package opened successfully");

        // Extract to temp directory first
        $extractPath = $this->tempDir . '/extract';

        // Check if extract directory exists and is writable
        if (!is_dir($extractPath) && !mkdir($extractPath, 0755, true)) {
            $zip->close();
            $this->writeToLog("ERROR: Failed to create extraction directory: " . $extractPath);
            throw new Exception('Failed to create extraction directory: ' . $extractPath);
        }

        if (!is_writable($extractPath)) {
            $zip->close();
            $this->writeToLog("ERROR: Extraction directory is not writable: " . $extractPath);
            throw new Exception('Extraction directory is not writable: ' . $extractPath);
        }

        // Try to extract
        $this->writeToLog("Extracting update package");
        if (!$zip->extractTo($extractPath)) {
            $error = error_get_last();
            $zip->close();
            $this->writeToLog("ERROR: Failed to extract update package: " . ($error['message'] ?? 'Unknown error'));
            throw new Exception('Failed to extract update package: ' . ($error['message'] ?? 'Unknown error'));
        }

        $zip->close();
        $this->writeToLog("Update package extracted successfully");

        // Verify update package structure and integrity
        try {
            $this->writeToLog("Verifying update package integrity...");
            if (!$this->verifyUpdatePackage()) {
                $this->writeToLog("ERROR: Update package verification failed");
                throw new Exception('Update package verification failed');
            }
            $this->writeToLog("Update package verified successfully");
        } catch (Exception $e) {
            // Clean up extracted files if verification fails
            $this->writeToLog("ERROR: " . $e->getMessage() . ". Cleaning up extracted files.");
            $this->removeDirectory($extractPath);
            throw $e; // Re-throw the exception after cleanup
        }

        return true;
    }

    /**
     * Install the updated files
     */
    private function installUpdate()
    {
        $extractPath = $this->tempDir . '/extract';

        try {
            // Copy new files to installation
            $this->writeToLog("Copying updated files to installation directory");
            $this->copyDirectory($extractPath, ABSPATH);
            $this->writeToLog("Files copied successfully");
            return true;
        } catch (Exception $e) {
            $this->writeToLog("ERROR: Installation failed: " . $e->getMessage());
            $this->logError('Installation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Utility Methods
     */
    private function makeApiRequest($url, $params)
    {
        $this->writeToLog("Making API request");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            $this->writeToLog("ERROR: API request failed: " . $error);
            return null;
        }
        
        $this->writeToLog("API response received with HTTP code: " . $httpCode);
        return json_decode($response, true);
    }

    private function downloadFile($url, $params, $target)
    {
        $this->writeToLog("Downloading file from");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // Add header info to response
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->writeToLog("ERROR: Download failed: " . $error);
            throw new Exception('Download failed: ' . $error);
        }

        $this->writeToLog("Download response received with HTTP code: " . $httpCode);

        // Split headers and body
        $headers = substr($response, 0, $headerSize);
        $remoteFile = substr($response, $headerSize);

        // Check if response is JSON (either by Content-Type header or content)
        $isJson = (
            strpos($headers, 'Content-Type: application/json') !== false ||
            substr(trim($remoteFile), 0, 1) === '{'
        );

        if ($isJson) {
            $json = json_decode($remoteFile, true);
            if ($json) {
                if (isset($json['status']) && $json['status'] === 'error') {
                    $this->writeToLog("ERROR: API Error: " . ($json['message'] ?? 'Unknown error'));
                    throw new Exception('API Error: ' . ($json['message'] ?? 'Unknown error'));
                }
                // Log unexpected JSON response
                $this->writeToLog("ERROR: Unexpected JSON response: " . $remoteFile);
                $this->logError('Unexpected JSON response: ' . $remoteFile);
                throw new Exception('Unexpected response format from server');
            }
        }

        // Check HTTP status code
        if ($httpCode !== 200) {
            $this->writeToLog("ERROR: Download failed with HTTP code: " . $httpCode);
            throw new Exception('Download failed with HTTP code: ' . $httpCode);
        }

        // Ensure target directory exists
        $targetDir = dirname($target);
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                $this->writeToLog("ERROR: Failed to create target directory: " . $targetDir);
                throw new Exception('Failed to create target directory: ' . $targetDir);
            }
            $this->writeToLog("Created target directory: " . $targetDir);
        }

        // Write file with better error handling
        $localFile = fopen($target, 'w');
        if (!$localFile) {
            $this->writeToLog("ERROR: Could not open file for writing: " . $target);
            throw new Exception('Could not open file for writing: ' . $target);
        }

        try {
            if (fwrite($localFile, $remoteFile) === false) {
                $this->writeToLog("ERROR: Failed to write to file: " . $target);
                throw new Exception('Failed to write to file: ' . $target);
            }
        } finally {
            fclose($localFile);
        }

        // Verify file was written
        if (!file_exists($target) || filesize($target) === 0) {
            $this->writeToLog("ERROR: File write verification failed: " . $target);
            throw new Exception('File write verification failed: ' . $target);
        }

        $this->writeToLog("File downloaded successfully");
        return $target;
    }

    private function cleanTempFiles()
    {
        $this->writeToLog("Cleaning temporary files");
        if (is_dir($this->tempDir . '/extract')) {
            $this->removeDirectory($this->tempDir . '/extract');
            $this->writeToLog("Removed extract directory");
        }
        if (file_exists($this->tempDir . '/update.zip')) {
            unlink($this->tempDir . '/update.zip');
            $this->writeToLog("Removed update.zip file");
        }
        if (is_dir($this->tempDir) && count(scandir($this->tempDir)) <= 2) {
            $this->removeDirectory($this->tempDir);
            $this->writeToLog("Removed empty temp directory");
        }
    }

    private function verifyUpdatePackage()
    {
        $params = [
            'action' => 'info',
            'version' => $this->targetVersion
        ];

        if (isset($_GET['test_update'])) {
            $params['test'] = true;
        }

        // Get hash from API for downloaded version
        $this->writeToLog("Requesting hash information from server for verification");
        $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

        if (!$response || !isset($response['status']) || $response['status'] !== 'success') {
            $this->writeToLog("ERROR: Failed to get update information from server: " . 
                (isset($response['message']) ? $response['message'] : 'Unknown error'));
            throw new Exception('Failed to get update information from server: ' .
                (isset($response['message']) ? $response['message'] : 'Unknown error'));
        }

        if (!isset($response['info']['hash'])) {
            $this->writeToLog("ERROR: Hash information not found in server response");
            throw new Exception('Hash information not found in server response');
        }

        // Calculate hash of the update package
        $this->writeToLog("Calculating hash of downloaded update package");
        $updateHash = hash_file('sha256', $this->tempDir . '/update.zip');

        // Verify hash
        if ($updateHash !== $response['info']['hash']) {
            $this->writeToLog("ERROR: Update package integrity check failed. Expected: " . 
                $response['info']['hash'] . " Got: " . $updateHash);
            throw new Exception('Update package integrity check failed. ' .
                'Expected: ' . $response['info']['hash'] . ' ' .
                'Got: ' . $updateHash);
        }

        $this->writeToLog("Hash verification successful");
        return true;
    }

    private function logError($message)
    {
        error_log('[SystemUpdater] ' . $message);
        
        // Also write to our dedicated log file if it exists
        if (file_exists($this->logFilePath)) {
            $this->writeToLog("ERROR: " . $message);
        }
    }

    private function copyDirectory($source, $dest)
    {
        $dir = opendir($source);
        if (!file_exists($dest)) {
            mkdir($dest);
            $this->writeToLog("Created directory: " . $dest);
        }
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') continue;

            $sourcePath = $source . '/' . $file;
            $destPath = $dest . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
        closedir($dir);
    }

    private function removeDirectory($dir)
    {
        if (!file_exists($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        return rmdir($dir);
    }
}