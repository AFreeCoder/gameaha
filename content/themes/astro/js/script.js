"use strict";
if (document.getElementById('tpl-comment-section')) {
    const gameId = document.getElementById('tpl-comment-section').getAttribute('data-id');
    const commentSystem = new CommentSystem(gameId);
}
const formSearch = document.querySelector("form.search-bar");
if (formSearch) {
	formSearch.addEventListener("submit", function (event) {
		event.preventDefault();
		const input = formSearch.querySelector("input[name='slug']");
		const sanitizedValue = input.value.replace(/ /g, "-");
		input.value = sanitizedValue;
		if (input.value.length >= 2) {
			formSearch.submit();
		}
	});
}
var isFullscreen = false;
function openFullscreen() {
	let game = document.getElementById("game-area");
	if (isFullscreen) {
		// Exit fullscreen
		isFullscreen = false;
		if (isMobileDevice()) {
			// game.style.position = "absolute";
			// document.getElementById("mobile-back-button").style.display = "none";
			// if (document.getElementById('allow_mobile_version')) {
			// 	document.getElementById("game-player").style.display = "none";
			// }
			// // Re-enable scrolling
			// document.body.style.overflow = "auto";
			// document.body.style.position = "static";
			// window.scrollTo(0, window.lastScrollPosition || 0);
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		} else {
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	} else {
		// Enter fullscreen
		isFullscreen = true;
		if (isMobileDevice()) {
			// Store current scroll position
			// window.lastScrollPosition = window.scrollY;

			// document.getElementById("game-player").style.display = "block";
			// game.style.position = "fixed";
			// document.getElementById("mobile-back-button").style.display = "flex";

			// // Prevent scrolling
			// document.body.style.overflow = "hidden";
			// document.body.style.position = "fixed";
			// document.body.style.width = "100%";
			// document.body.style.height = "100%";

			// // Hide URL bar by scrolling up slightly
			// setTimeout(() => {
			// 	window.scrollTo(0, 1);
			// }, 100);
			if (game.requestFullscreen) {
				game.requestFullscreen();
			} else if (game.mozRequestFullScreen) {
				game.mozRequestFullScreen();
			} else if (game.webkitRequestFullscreen) {
				game.webkitRequestFullscreen();
			} else if (game.msRequestFullscreen) {
				game.msRequestFullscreen();
			}
		} else {
			if (game.requestFullscreen) {
				game.requestFullscreen();
			} else if (game.mozRequestFullScreen) {
				game.mozRequestFullScreen();
			} else if (game.webkitRequestFullscreen) {
				game.webkitRequestFullscreen();
			} else if (game.msRequestFullscreen) {
				game.msRequestFullscreen();
			}
		}
	}
}
function isMobileDevice() {
	if (navigator.userAgent.match(/Android/i)
		|| navigator.userAgent.match(/webOS/i)
		|| navigator.userAgent.match(/iPhone/i)
		|| navigator.userAgent.match(/iPad/i)
		|| navigator.userAgent.match(/iPod/i)
		|| navigator.userAgent.match(/BlackBerry/i)
		|| navigator.userAgent.match(/Windows Phone/i)) {
		return true;
	} else {
		return false;
	}
}

if (document.querySelector('iframe#game-area')) {
    loadLeaderboard({ type: 'top', amount: 10 });
}

function loadLeaderboard(conf) {
    const leaderboardElement = document.getElementById('content-leaderboard');
    if (leaderboardElement) {
        const gameId = leaderboardElement.dataset.id;
        
        fetch('/includes/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'get_scoreboard',
                'game-id': gameId,
                'conf': JSON.stringify(conf)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.length) {
                renderLeaderboard(data);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function renderLeaderboard(data) {
    let html = `
        <table class="w-full">
            <thead>
                <tr class="text-left">
                    <th scope="col" class="py-2 px-4">#</th>
                    <th scope="col" class="py-2 px-4">Username</th>
                    <th scope="col" class="py-2 px-4">Score</th>
                    <th scope="col" class="py-2 px-4">Date</th>
                </tr>
            </thead>
            <tbody>`;
            
    data.forEach((item, index) => {
        html += `
            <tr class="border-t border-gray-700">
                <th scope="row" class="py-2 px-4">${index + 1}</th>
                <td class="py-2 px-4">${item.username}</td>
                <td class="py-2 px-4">${item.score}</td>
                <td class="py-2 px-4">${item.created_date.substr(0, 10)}</td>
            </tr>`;
    });
    
    html += '</tbody></table>';

    document.getElementById('subheading-leaderboard').style.display = 'flex';
    document.getElementById('content-leaderboard').innerHTML = html;
}

// Helper function to handle voting actions
function handleVoteAction(action, button) {
    const gameContent = document.querySelector('.game-content');
    const dataId = gameContent.getAttribute('data-id');
    
    // Disable button immediately to prevent double-clicks
    button.disabled = true;
    
    const formData = new FormData();
    formData.append('id', dataId);
    formData.append('action', action);
    formData.append(action, true);
    
    if (action === 'favorite') {
        formData.append('favorite', true);
    } else {
        formData.append('vote', true);
    }

    fetch('/includes/vote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Update button states based on action
        if (action === 'favorite') {
            const svg = button.querySelector('svg');
            if (svg) {
                svg.setAttribute('fill', 'currentColor');
                button.classList.remove('text-gray-400', 'hover:text-violet-500');
                button.classList.add('text-red-500', 'hover:text-red-600');
            }
        } else if (action === 'upvote' || action === 'downvote') {
            // Add active state styles
            button.classList.remove('text-gray-400', 'hover:text-violet-500');
            button.classList.add('text-violet-500');
            
            // If upvote was clicked, disable downvote and vice versa
            const oppositeButton = action === 'upvote' 
                ? document.querySelector('#downvote')
                : document.querySelector('#upvote');
            
            if (oppositeButton) {
                oppositeButton.disabled = true;
                oppositeButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Add visual feedback
        button.classList.add('opacity-75');
    })
    .catch(error => {
        console.error('Error:', error);
        // Re-enable button on error
        button.disabled = false;
        button.classList.remove('opacity-75');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Favorite button handler
    const favoriteBtn = document.querySelector('.single-game__actions #favorite');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!favoriteBtn.disabled) {
                handleVoteAction('favorite', favoriteBtn);
            }
        });
    }

    // Upvote button handler
    const upvoteBtn = document.querySelector('.single-game__actions #upvote');
    if (upvoteBtn) {
        upvoteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!upvoteBtn.disabled) {
                handleVoteAction('upvote', upvoteBtn);
            }
        });
    }

    // Downvote button handler
    const downvoteBtn = document.querySelector('.single-game__actions #downvote');
    if (downvoteBtn) {
        downvoteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (!downvoteBtn.disabled) {
                handleVoteAction('downvote', downvoteBtn);
            }
        });
    }
});

(function() {
    // Helper function to handle smooth scrolling
    function smoothScroll(element, amount, duration) {
        const start = element.scrollLeft;
        const startTime = performance.now();
        
        function easeInOutQuad(t) {
            return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
        }

        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            element.scrollLeft = start + (amount * easeInOutQuad(progress));
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }
        
        requestAnimationFrame(animate);
    }

    // Set up scroll button handlers
    const scrollButtons = [
        { prev: 'btn_prev', next: 'btn_next', container: '.profile-gamelist ul' },
        { prev: 'f_prev', next: 'f_next', container: '.favorite-gamelist ul' },
        { prev: 't-prev', next: 't-next', container: '.list-1-wrapper' }
    ];

    scrollButtons.forEach(({ prev, next, container }) => {
        const prevButton = document.getElementById(prev);
        const nextButton = document.getElementById(next);
        const scrollContainer = document.querySelector(container);

        if (prevButton && scrollContainer) {
            prevButton.addEventListener('click', () => {
                smoothScroll(scrollContainer, -150, 300);
            });
        }

        if (nextButton && scrollContainer) {
            nextButton.addEventListener('click', () => {
                smoothScroll(scrollContainer, 150, 300);
            });
        }
    });

    // Set up comment deletion
    document.querySelectorAll('.delete-comment').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            fetch('/includes/comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    delete: true,
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                // Success handler - kept empty as per original
            })
            .catch(error => {
                // Error handler - kept empty as per original
            })
            .finally(data => {
                if (data?.responseText === 'deleted') {
                    const commentElement = document.querySelector('.id-' + id);
                    if (commentElement) {
                        commentElement.remove();
                    }
                }
            });
        });
    });
})();

if(document.getElementById('view-all-categories')){
    document.getElementById('view-all-categories').addEventListener('click', function() {
        const allCategories = document.getElementById('all-categories');
        const isHidden = allCategories.classList.contains('hidden');

        if (isHidden) {
            allCategories.classList.remove('hidden');
            this.textContent = this.getAttribute('data-label-collapse');
        } else {
            allCategories.classList.add('hidden');
            this.textContent = this.getAttribute('data-label-expand');
        }
    });
}