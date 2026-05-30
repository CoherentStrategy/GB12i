/* ---------- HELPERS ---------- */
const $ = id => document.getElementById(id);
/* ---------- POPUPS ---------- */
let popupOpen1=false,popupOpen2=false

function openPopup1(html){ $("popupContent1").innerHTML=html; $("popupOverlay1").classList.remove("hidden"); $("popupOverlay1").classList.add("is-visible"); }
function closePopup1(){ $("popupOverlay1").classList.remove("is-visible"); $("popupOverlay1").classList.add("hidden"); }

function openPopup2(html){ $("popupContent2").innerHTML=html; $("popupOverlay2").classList.remove("hidden"); $("popupOverlay2").classList.add("is-visible"); }
function closePopup2(){ $("popupOverlay2").classList.remove("is-visible"); $("popupOverlay2").classList.add("hidden"); }

const backdrop = $("ad-backdrop");

function openAd() {
    backdrop.classList.add("is-visible");
    backdrop.hidden = false;
}


/* ---------- ----------*/
const register = document.getElementsByClassName("register")[0];
const login = document.getElementsByClassName("login")[0];

$('registerBtn').addEventListener('click', () => {
    $('container').classList.remove("active");
    register.inert = false; // makes login useless
    login.inert = true;
});

$('loginBtn').addEventListener('click', () => {
    $('container').classList.add("active");
    register.inert = true; // makes register useless
    login.inert = false;
});

$("forgotPassword").addEventListener("click", (e) => {
    e.preventDefault(); // stop page jump
    forgotPassword();
});

function forgotPassword(){
    openAd();
};

window.addEventListener("message", (e) => {
    if (e.data?.type === "close-ad") {
        backdrop.classList.remove("is-visible");
        backdrop.hidden = true;

        // Force the iframe to reload by resetting the src
        const adFrame = $("ad-wrapper");
        const currentSrc = adFrame.src;
        adFrame.src = ""; // Clear it first
        adFrame.src = currentSrc; // Re-assign the original URL to trigger reload
    }
});

/* ----------- ANTI-CHEAT TABS MANAGEMENT----------- */
const channel = new BroadcastChannel('game_session_channel');
        let isTabActive = true;

        // Unique ID for this specific tab to keep track of who owns what
        const tabId = Math.random().toString(36).substring(2, 9);
        $('session-id-display').innerText = `Tab ID: ${tabId}`; // Change id if nessesary

        // 1. When loading a new tab, check if another tab is already active
        channel.postMessage({ type: 'PING_EXISTING_TABS', senderId: tabId });

        channel.onmessage = (event) => {
            const { type, senderId } = event.data;

            // Skip messages sent by this tab itself
            if (senderId === tabId) return;

            if (type === 'PONG_TAB_ACTIVE') {
                // An older tab responded! That means THIS new tab is a duplicate. Show the prompt.
                if (isTabActive) {
                    showDuplicatePrompt();
                }
            }

            if (type === 'PING_EXISTING_TABS') {
                // A new tab is asking if anyone is here. If we are active, say yes.
                if (isTabActive) {
                    channel.postMessage({ type: 'PONG_TAB_ACTIVE', senderId: tabId });
                }
            }

            if (type === 'FORCE_EVICT_OTHER_TABS') {
                // Another tab clicked "Use This Tab Instead". We must shut down immediately.
                if (isTabActive) {
                    forceCloseSession();
                }
            }
        };

        // STEP A: Show the prompt with the "Use This Tab" button (Runs on the NEW tab)
        function showDuplicatePrompt() {
            isTabActive = false;
            // Show overlay, but keep the button interactive
            $('duplicate-overlay').style.display = 'flex';
            $('takeover-button').style.display = 'inline-block';
        }

        // STEP B: User clicks the button (Runs on the NEW tab)
        function claimSession() {
            isTabActive = true;
            
            // Hide the overlay and resume game functions
            $('duplicate-overlay').style.display = 'none';
            
            // Tell all other tabs to immediately evict themselves
            channel.postMessage({ type: 'FORCE_EVICT_OTHER_TABS', senderId: tabId });
            
            // Reconnect your game WebSockets or initialize state here
            console.log("Session claimed by tab:", tabId);
        }

        // STEP C: Lock down the tab completely (Runs on the OLD tab that got evicted)
        function forceCloseSession() {
            isTabActive = false;
            
            // Disconnect your WebSocket/Game engine connection here
            // example: ws.close();
            
            // Show the error overlay
            $('duplicate-overlay').style.display = 'flex';
            
            // Change text to reflect that this tab was killed by another one
            const textElement = document.querySelector('.warning-box p');
            textElement.innerText = "This session was taken over by another tab. Close this window to continue previous session.";
            
            // Hide the takeover button on this tab so they can't bounce back and forth endlessly
            $('takeover-button').style.display = 'none';

            // Fully freeze input mechanics
            window.addEventListener('keydown', blockEvent, true);
            window.addEventListener('mousedown', blockEvent, true);
        }

        function blockEvent(e) {
            // Allow clicking inside the warning box elements only if active, else block everything
            if (!isTabActive) {
                e.stopPropagation();
                e.preventDefault();
            }
        }
