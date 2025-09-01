<?php

if(!has_admin_access()){
	die();
}

?>
<div class="section" id="fetch">
		<div class="mb-3">
			<label class="form-label">Distributor</label> 
			<select name="distributor" class="form-control" id="distributor-options">
				<option value="" disabled selected hidden>Choose game distributor...</option>
				<option value="#gamemonetize">GameMonetize</option>
				<option value="#gamepix">GamePix</option>
				<option value="#4j">4J</option>
				<option value="#wanted5games">Wanted5Games</option>
				<option value="#gamearter">GameArter</option>
				<option value="#gameflare">Gameflare</option>
				<option value="#y8">Y8</option>
				<option value="#gamezop">Gamezop</option>
				<option value="#htmlgames">HTMLGAMES</option>
			</select>
		</div>
		<div class="fetch-games tab-container fade" id="gamemonetize">
			<div class="alert alert-info alert-dismissible fade show" role="alert">No registration required. more info <a href="https://gamemonetize.com/publishers" target="_blank">GameMonetize</a>.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 512x384px thumbnail for both thumb_1 & thumb_2, also there is no multiple category.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-gamemonetize" class="_gamemonetize">
				<div class="mb-3">
					<label class="form-label">Category</label> 
					<select name="Category" class="form-control">
						<option value="All" selected>All</option>
						<option value="1">.IO</option>
						<option value="2">2 Player</option>
						<option value="3">3D</option>
						<option value="0">Action</option>
						<option value="4">Adventure</option>
						<option value="5">Arcade</option>
						<option value="19">Baby Hazel</option>
						<option value="6">Bejeweled</option>
						<option value="7">Boys</option>
						<option value="8">Clicker</option>
						<option value="9">Cooking</option>
						<option value="10">Girls</option>
						<option value="11">Hypercasual</option>
						<option value="12">Multiplayer</option>
						<option value="13">Puzzle</option>
						<option value="14">Racing</option>
						<option value="15">Shooting</option>
						<option value="16">Soccer</option>
						<option value="17">Sports</option>
						<option value="18">Stickman</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="gamepix">
			<div class="alert alert-warning alert-dismissible fade show" role="alert">You need joined <a href="https://company.gamepix.com/publishers/" target="_blank">GamePix</a> publisher program to be able to publish their games on your site.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">thumb_1 uses 320x200px image and thumb_2 uses 105x105px image, both image resolution is not "perfect" for current official themes. Also the reason why GamePix listed here.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">Use the "GamePix SID" plugin to change your game url SID.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-gamepix" class="gamepix">
				<input type="hidden" name="vfetch" value="v2">
				<div class="mb-3">
					<label class="form-label">Sort By</label> 
					<select name="Sort" class="form-control">
						<option value="newest" selected>Newest</option>
						<option value="quality">Quality</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Category</label> 
					<select name="category" id="category" class="form-control" required>
					  <option value="all" selected>All</option>
					  <option value="2048">2048</option>
					  <option value="action">Action</option>
					  <option value="addictive">Addictive</option>
					  <option value="adventure">Adventure</option>
					  <option value="airplane">Airplane</option>
					  <option value="animal">Animal</option>
					  <option value="anime">Anime</option>
					  <option value="arcade">Arcade</option>
					  <option value="archery">Archery</option>
					  <option value="ball">Ball</option>
					  <option value="basketball">Basketball</option>
					  <option value="battle">Battle</option>
					  <option value="battle-royale">Battle royale</option>
					  <option value="bejeweled">Bejeweled</option>
					  <option value="bingo">Bingo</option>
					  <option value="block">Block</option>
					  <option value="board">Board</option>
					  <option value="bowling">Bowling</option>
					  <option value="boxing">Boxing</option>
					  <option value="brain">Brain</option>
					  <option value="bubble-shooter">Bubble shooter</option>
					  <option value="building">Building</option>
					  <option value="car">Car</option>
					  <option value="card">Card</option>
					  <option value="casino">Casino</option>
					  <option value="casual">Casual</option>
					  <option value="cats">Cats</option>
					  <option value="chess">Chess</option>
					  <option value="christmas">Christmas</option>
					  <option value="city-building">City building</option>
					  <option value="classics">Classics</option>
					  <option value="clicker">Clicker</option>
					  <option value="coco">Coco</option>
					  <option value="coloring">Coloring</option>
					  <option value="cooking">Cooking</option>
					  <option value="cool">Cool</option>
					  <option value="crazy">Crazy</option>
					  <option value="crypto-and-blockchain">Crypto and blockchain</option>
					  <option value="dinosaur">Dinosaur</option>
					  <option value="dirt-bike">Dirt bike</option>
					  <option value="drawing">Drawing</option>
					  <option value="dress-up">Dress up</option>
					  <option value="drifting">Drifting</option>
					  <option value="driving">Driving</option>
					  <option value="educational">Educational</option>
					  <option value="escape">Escape</option>
					  <option value="fantasy-flight">Fantasy flight</option>
					  <option value="farming">Farming</option>
					  <option value="fashion">Fashion</option>
					  <option value="fighting">Fighting</option>
					  <option value="fire-and-water">Fire and water</option>
					  <option value="first-person-shooter">First person shooter</option>
					  <option value="fishing">Fishing</option>
					  <option value="flight">Flight</option>
					  <option value="fun">Fun</option>
					  <option value="games-for-girls">Games for girls</option>
					  <option value="gangster">Gangster</option>
					  <option value="golf">Golf</option>
					  <option value="granny">Granny</option>
					  <option value="gun">Gun</option>
					  <option value="hair-salon">Hair salon</option>
					  <option value="halloween">Halloween</option>
					  <option value="hidden-object">Hidden object</option>
					  <option value="horror">Horror</option>
					  <option value="horse">Horse</option>
					  <option value="hunting">Hunting</option>
					  <option value="hyper-casual">Hyper casual</option>
					  <option value="idle">Idle</option>
					  <option value="io">Io</option>
					  <option value="jewel">Jewel</option>
					  <option value="jigsaw-puzzles">Jigsaw puzzles</option>
					  <option value="jumping">Jumping</option>
					  <option value="junior">Junior</option>
					  <option value="kids">Kids</option>
					  <option value="knight">Knight</option>
					  <option value="mahjong">Mahjong</option>
					  <option value="makeup">Makeup</option>
					  <option value="management">Management</option>
					  <option value="mario">Mario</option>
					  <option value="match-3">Match 3</option>
					  <option value="math">Math</option>
					  <option value="memory">Memory</option>
					  <option value="minecraft">Minecraft</option>
					  <option value="mining">Mining</option>
					  <option value="mmorpg">Mmorpg</option>
					  <option value="mobile">Mobile</option>
					  <option value="money">Money</option>
					  <option value="monster">Monster</option>
					  <option value="motorcycle">Motorcycle</option>
					  <option value="music">Music</option>
					  <option value="ninja">Ninja</option>
					  <option value="ninja-turtle">Ninja turtle</option>
					  <option value="offroad">Offroad</option>
					  <option value="parking">Parking</option>
					  <option value="parkour">Parkour</option>
					  <option value="pirates">Pirates</option>
					  <option value="pixel">Pixel</option>
					  <option value="platformer">Platformer</option>
					  <option value="poker">Poker</option>
					  <option value="police">Police</option>
					  <option value="pool">Pool</option>
					  <option value="princess">Princess</option>
					  <option value="puzzle">Puzzle</option>
					  <option value="racing">Racing</option>
					  <option value="retro">Retro</option>
					  <option value="robots">Robots</option>
					  <option value="rpg">Rpg</option>
					  <option value="runner">Runner</option>
					  <option value="scary">Scary</option>
					  <option value="scrabble">Scrabble</option>
					  <option value="sharks">Sharks</option>
					  <option value="shooter">Shooter</option>
					  <option value="simulation">Simulation</option>
					  <option value="skateboard">Skateboard</option>
					  <option value="skibidi-toilet">Skibidi toilet</option>
					  <option value="skill">Skill</option>
					  <option value="slot">Slot</option>
					  <option value="snake">Snake</option>
					  <option value="sniper">Sniper</option>
					  <option value="soccer">Soccer</option>
					  <option value="spinner">Spinner</option>
					  <option value="sports">Sports</option>
					  <option value="stickman">Stickman</option>
					  <option value="strategy">Strategy</option>
					  <option value="surgery">Surgery</option>
					  <option value="survival">Survival</option>
					  <option value="tanks">Tanks</option>
					  <option value="tap">Tap</option>
					  <option value="tetris">Tetris</option>
					  <option value="trivia">Trivia</option>
					  <option value="two-player">Two player</option>
					  <option value="war">War</option>
					  <option value="word">Word</option>
					  <option value="world-cup">World cup</option>
					  <option value="worm">Worm</option>
					  <option value="zombie">Zombie</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="12">12</option>
						<option value="24">24</option>
						<option value="48">48</option>
						<option value="96">96</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="<?php _e('Fetch games') ?>"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="4j">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about 4J <a href="https://w.4j.com/" target="_blank">w.4j.com</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 180x135px thumbnail for both thumb_1 & thumb_2, also there is no multiple category.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-4j" class="_4j">
				<div class="mb-3">
					<label class="form-label">Tag</label>
					<select name="Tag" class="form-control">
						<option value=0 selected>All</option>
						<option value=282>.io</option>
						<option value=6>2 Player</option>
						<option value=7>3D</option>
						<option value=8>Action</option>
						<option value=9>Adventure</option>
						<option value=11>Aircraft</option>
						<option value=12>Alien</option>
						<option value=295>Among Us</option>
						<option value=10>Android</option>
						<option value=13>Animal</option>
						<option value=14>Anime</option>
						<option value=15>Arcade</option>
						<option value=16>Arkanoid</option>
						<option value=17>Army</option>
						<option value=293>ASMR</option>
						<option value=18>Asteroid</option>
						<option value=255>Avoid</option>
						<option value=19>Baby</option>
						<option value=269>Baby Hazel</option>
						<option value=20>Balance</option>
						<option value=21>Ball</option>
						<option value=22>Balloon</option>
						<option value=23>Barbie</option>
						<option value=24>Baseball</option>
						<option value=25>Basketball</option>
						<option value=250>Batman</option>
						<option value=26>Ben 10</option>
						<option value=27>Bike</option>
						<option value=28>Billiard</option>
						<option value=29>Bird</option>
						<option value=30>Block</option>
						<option value=32>BMX</option>
						<option value=33>Boat</option>
						<option value=34>Bomb</option>
						<option value=35>Bounce</option>
						<option value=36>Bow</option>
						<option value=37>Bowling</option>
						<option value=38>Boxing</option>
						<option value=39>Boy</option>
						<option value=42>Brain</option>
						<option value=40>Bricks</option>
						<option value=41>Bridge</option>
						<option value=44>Bubble</option>
						<option value=43>Bubble Shooter</option>
						<option value=46>Burger</option>
						<option value=47>Business</option>
						<option value=48>Cake</option>
						<option value=49>Car</option>
						<option value=50>Card</option>
						<option value=51>Cartoon</option>
						<option value=277>Cartoon Network</option>
						<option value=52>Casino</option>
						<option value=53>Castle</option>
						<option value=54>Cat</option>
						<option value=55>Celebrity</option>
						<option value=56>Checkers</option>
						<option value=57>Chess</option>
						<option value=59>Chicken</option>
						<option value=58>Christmas</option>
						<option value=256>Cleaning</option>
						<option value=60>Collecting</option>
						<option value=61>Coloring</option>
						<option value=62>Cooking</option>
						<option value=63>Cricket</option>
						<option value=64>Cute</option>
						<option value=66>Dancing</option>
						<option value=65>Dating</option>
						<option value=67>Decorate</option>
						<option value=68>Defense</option>
						<option value=262>Design</option>
						<option value=69>Detective</option>
						<option value=70>Dice</option>
						<option value=71>Difference</option>
						<option value=72>Dinosaur</option>
						<option value=73>Disney</option>
						<option value=74>Doctor</option>
						<option value=75>Domino</option>
						<option value=76>Dora</option>
						<option value=77>Dragon</option>
						<option value=78>Dragon Ball Z</option>
						<option value=79>Drawing</option>
						<option value=80>Dress Up</option>
						<option value=81>Driving</option>
						<option value=82>Dungeon</option>
						<option value=83>Dwarf</option>
						<option value=284>Editor Choice</option>
						<option value=84>Educational</option>
						<option value=279>Elsa</option>
						<option value=85>Escape</option>
						<option value=274>Ever After High</option>
						<option value=86>Fairy</option>
						<option value=87>Fantasy</option>
						<option value=88>Farm</option>
						<option value=89>Fashion</option>
						<option value=90>Fighting</option>
						<option value=91>Fire</option>
						<option value=92>Fish</option>
						<option value=93>Fishing</option>
						<option value=94>Flight</option>
						<option value=95>Food</option>
						<option value=96>Football</option>
						<option value=249>Frozen</option>
						<option value=97>Fruit</option>
						<option value=98>Funny</option>
						<option value=290>GameDistribution</option>
						<option value=261>Ghost</option>
						<option value=99>Girl</option>
						<option value=100>Gold</option>
						<option value=101>Golf</option>
						<option value=102>Grooming</option>
						<option value=103>Guessing</option>
						<option value=104>Gun</option>
						<option value=105>Hair</option>
						<option value=106>Halloween</option>
						<option value=107>Helicopter</option>
						<option value=266>Hello Kitty</option>
						<option value=108>Hidden</option>
						<option value=254>Highscore</option>
						<option value=109>Historical</option>
						<option value=110>Hockey</option>
						<option value=111>Holiday</option>
						<option value=112>Horse</option>
						<option value=113>House</option>
						<option value=114>HTML5</option>
						<option value=115>Hunting</option>
						<option value=117>Ice</option>
						<option value=116>Interactive Fiction</option>
						<option value=118>Island</option>
						<option value=119>Jewel</option>
						<option value=120>Jigsaw</option>
						<option value=121>Jumping</option>
						<option value=122>Kart</option>
						<option value=123>Kids</option>
						<option value=124>Killing</option>
						<option value=125>Kissing</option>
						<option value=126>Knight</option>
						<option value=294>Kogama</option>
						<option value=127>Launch</option>
						<option value=267>Lego</option>
						<option value=128>Letters</option>
						<option value=129>Love</option>
						<option value=130>Magic</option>
						<option value=131>Mahjong</option>
						<option value=132>Makeover / Make-up</option>
						<option value=133>Management</option>
						<option value=134>Mario</option>
						<option value=291>Masha And The Bear</option>
						<option value=135>Match 3</option>
						<option value=136>Matching</option>
						<option value=137>Math</option>
						<option value=138>Maze</option>
						<option value=139>Memory</option>
						<option value=280>Mermaid</option>
						<option value=140>Mine</option>
						<option value=281>Minecraft</option>
						<option value=253>Minion</option>
						<option value=141>MMO</option>
						<option value=142>Monkey</option>
						<option value=143>Monster</option>
						<option value=271>Monster High</option>
						<option value=144>Monster Truck</option>
						<option value=145>Motorcycle</option>
						<option value=147>Movie</option>
						<option value=148>Multiplayer</option>
						<option value=149>Multiplication</option>
						<option value=150>Music</option>
						<option value=260>Nail</option>
						<option value=151>Ninja</option>
						<option value=268>Ninjago</option>
						<option value=152>Number</option>
						<option value=153>Obstacle</option>
						<option value=289>Paint</option>
						<option value=154>Panda</option>
						<option value=155>Parking</option>
						<option value=287>Party</option>
						<option value=156>Penguin</option>
						<option value=288>Pet</option>
						<option value=160>Physics</option>
						<option value=161>Pinball</option>
						<option value=162>Pipe</option>
						<option value=163>Pirate</option>
						<option value=164>Pixel</option>
						<option value=165>Plane</option>
						<option value=157>Planet</option>
						<option value=158>Plant</option>
						<option value=159>Platform</option>
						<option value=166>Point And Click</option>
						<option value=276>Pokemon</option>
						<option value=167>Poker</option>
						<option value=168>Police</option>
						<option value=169>Pong</option>
						<option value=265>Pony</option>
						<option value=170>Pool</option>
						<option value=257>Pou</option>
						<option value=171>Princess</option>
						<option value=172>Prison</option>
						<option value=173>Punch</option>
						<option value=174>Puzzle</option>
						<option value=175>PVP</option>
						<option value=176>Quiz</option>
						<option value=177>Rabbit</option>
						<option value=178>Racing</option>
						<option value=179>Relaxation</option>
						<option value=180>Rescue</option>
						<option value=181>Restaurant</option>
						<option value=182>Retro</option>
						<option value=183>Rhythm</option>
						<option value=184>Robot</option>
						<option value=185>Rocket</option>
						<option value=187>Room</option>
						<option value=186>RPG</option>
						<option value=188>Running</option>
						<option value=189>Samurai</option>
						<option value=190>School</option>
						<option value=191>Science</option>
						<option value=192>Seduction</option>
						<option value=193>Shark</option>
						<option value=194>Shoot 'Em Up</option>
						<option value=195>Shooting</option>
						<option value=196>Shopping</option>
						<option value=197>Side Scrolling</option>
						<option value=227>Simpson</option>
						<option value=198>Simulation</option>
						<option value=199>Skateboard</option>
						<option value=200>Skating</option>
						<option value=201>Ski</option>
						<option value=259>Skill</option>
						<option value=264>Slacking</option>
						<option value=202>Snake</option>
						<option value=203>Sniper</option>
						<option value=204>Snow</option>
						<option value=205>Soccer</option>
						<option value=272>Sofia</option>
						<option value=206>Solitaire</option>
						<option value=207>Sonic</option>
						<option value=208>Space</option>
						<option value=209>Spaceship</option>
						<option value=210>Spiderman</option>
						<option value=211>Spongebob</option>
						<option value=212>Sports</option>
						<option value=278>Star Wars</option>
						<option value=213>Stealth</option>
						<option value=214>Stickman</option>
						<option value=215>Strategy</option>
						<option value=216>Street Fighting</option>
						<option value=217>Stunts</option>
						<option value=218>Submachine</option>
						<option value=292>Submachine Gun</option>
						<option value=219>Submarine</option>
						<option value=221>Sudoku</option>
						<option value=222>Superhero</option>
						<option value=252>Surgery</option>
						<option value=223>Sword</option>
						<option value=270>Talking Tom</option>
						<option value=224>Tank</option>
						<option value=225>Tennis</option>
						<option value=226>Tetris</option>
						<option value=228>Timing</option>
						<option value=229>Tower Defense</option>
						<option value=230>Train</option>
						<option value=275>Transformers</option>
						<option value=231>Truck</option>
						<option value=232>Turn Based</option>
						<option value=233>Turtle</option>
						<option value=234>Tutorials</option>
						<option value=235>Typing</option>
						<option value=237>Undead</option>
						<option value=236>Unity3D</option>
						<option value=238>Vampire</option>
						<option value=239>Violence</option>
						<option value=240>Virus</option>
						<option value=241>Volleyball</option>
						<option value=242>War</option>
						<option value=286>Wedding</option>
						<option value=251>Whack</option>
						<option value=243>Witch</option>
						<option value=244>Wizard</option>
						<option value=245>Wolf</option>
						<option value=246>Word</option>
						<option value=247>Worm</option>
						<option value=248>Zombie</option>
						<option value=258>Zuma</option>
					</select>
					<label class="form-label">Item</label>
					<select name="Limit" class="form-control">
						<option selected="selected" value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="wanted5games">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about Wanted5Games <a href="https://wanted5games.com/publishers/" target="_blank">wanted5games.com</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 270x196px thumbnail for both thumb_1 & thumb_2.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-wanted5games" class="_wanted5games">
				<div class="mb-3">
					<label class="form-label">Category</label>
					<select name="category" class="form-control">
						<option value="" selected="">All</option>
						<option value="action">Action</option>
						<option value="adventure">Adventure</option>
						<option value="racing">Racing</option>
						<option value="shooting">Shooting</option>
						<option value="strategy">Strategy</option>
						<option value="arcade">Arcade</option>
						<option value="tower-defense">Tower Defense</option>
						<option value="dress-up">Dress up</option>
						<option value="cooking">Cooking</option>
						<option value="animals">Animals</option>
						<option value="kissing">Kissing</option>
						<option value="educational">Educational</option>
						<option value="board-and-card">Board and Card</option>
						<option value="sports">Sports</option>
						<option value="platform">Platform</option>
						<option value="skill">Skill</option>
						<option value="bubble-shooter">Bubble Shooter</option>
						<option value="mahjong">Mahjong</option>
						<option value="match-3">Match 3</option>
						<option value="time-management">Time Management</option>
						<option value="sudoku">Sudoku</option>
						<option value="bingo">Bingo</option>
						<option value="casino">Casino</option>
						<option value="quiz">Quiz</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Item</label>
					<select name="limit" class="form-control">
						<option selected="selected" value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="gamearter">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about GameArter <a href="https://www.gamearter.com/partners" target="_blank">gamearter.com</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 460x344px thumbnail for both thumb_1 & thumb_2.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">Most GameArter games is Desktop only and not playable on mobile devices.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">Publisher registration is required.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-gamearter" class="_gamearter">
				<div class="mb-3">
					<label class="form-label">Category</label>
					<select name="category" class="form-control">
						<option value="" selected="">All</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Item</label>
					<select name="limit" class="form-control">
						<option selected="selected" value="20">20</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
						<option value="1000">1000</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label>
					<select name="offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="gameflare">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about Gameflare <a href="https://distribution.gameflare.com/" target="_blank">gameflare.com</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 400x300px thumbnail for both thumb_1 & thumb_2.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-gameflare" class="_gameflare">
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="70">70</option>
						<option value="100">100</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="y8">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about Y8 <a href="https://www.y8.com/games_for_your_website" target="_blank">y8.com</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">There is no 512x512px thumbails, only 180x135px gif thumbnail for both thumb_1 & thumb_2.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-y8" class="_y8">
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="70">70</option>
						<option value="100">100</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="gamezop">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about Gamezop <a href="https://business.gamezop.com/publishers" target="_blank">business.gamezop.com/publishers</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<div class="alert alert-warning alert-dismissible fade show" role="alert">You need a publisher ID and must be registered as a Gamezop publisher.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-gamezop" class="_gamezop">
				<div class="mb-3">
					<label class="form-label">Your ID</label> 
					<input type="text" class="form-control" name="pub-id" value="7000" required>
				</div>
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="70">70</option>
						<option value="100">100</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<div class="fetch-games tab-container fade" id="htmlgames">
			<div class="alert alert-info alert-dismissible fade show" role="alert">More info about <a href="https://www.htmlgames.com/html5-games-for-your-site/" target="_blank">HTMLGAMES.COM</a><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
			<form id="form-fetch-htmlgames" class="_htmlgames">
				<div class="mb-3">
					<label class="form-label">Item</label> 
					<select name="Limit" class="form-control">
						<option selected="selected" value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="70">70</option>
						<option value="100">100</option>
						<option value="100">200</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Offset</label> 
					<select name="Offset" class="form-control">
						<option selected="selected" value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary btn-md" value="Fetch games"/>
			</form>
		</div>
		<br>
		<div class="fetch-loading" style="display: none;">
			<h4>Fetching games ...</h4>
		</div>
		<div id="action-info"></div>
		<div class="fetch-list" style="display: none;">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Thumbnail</th>
							<th>Game Name</th>
							<th>Category</th>
							<th>URL</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="gameList">
					</tbody>
				</table>
			</div>
			<button class="btn btn-primary btn-md" id="add-all">Add all</button>
		</div>
		<div class="div-stop" style="display: none;">
			<button class="btn btn-danger btn-md" id="stop-add"><?php _e('Stop') ?></button>
		</div>
	</div>
<input type="hidden" name="p_code" value="<?php echo (ADMIN_DEMO ? 'holy-moly' : check_purchase_code()) ?>" id="p_code" />

<script type="text/javascript">
	"use strict";
	$(document).ready(function(){
		$( "form" ).submit(function( event ) {
			let arr = $( this ).serializeArray();
			let source = $(this).attr('class');
			if(source === '_gamemonetize' || source === '_gamedistribution' || source === '_4j' || source === '_wanted5games'  || source === '_gamearter' || source === '_gameflare' || source === '_y8' || source === '_gamezop' || source === '_htmlgames'){
				event.preventDefault();
				let code = $("#p_code").val();
				distributor = $(this).attr('class');
				if(distributor){
					let url = 'https://api.cloudarcade.net/v2/fetch-games.php?action=fetch&source='+distributor+'&data='+simple_array(arr)+'&code='+code;
					getGame.getList(url).then((res)=>{
						if(res['status']){
							if(res['status'] == 'error' || res['status'] == 'failed'){
								show_action_info('error - '+res['message']);
							} else {
								console.log(res);
								alert('Failed! check console log for more info');
							}
						} else {
							getGame.generateList(res);
						}
					});
				}	
			}
		});
		function simple_array(arr){
			let tmp = [];
			arr.forEach((item)=>{
				tmp.push(item.value);
			});
			return JSON.stringify(tmp);
		}
	});
</script>