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

/* ----------- ----------- */
const channel = new BroadcastChannel('app_tab_auth');
channel.postMessage({ type : 'NEW_TAB_OPENED'});

channel.onmessage = (event) => {
    if (channel.data.type === 'NEW_TAB_OPENED') {
        channel.postMessage({ type : 'ALREADY_OPEN'});
    }

    if (channel.data.type === 'ALREADY_OPEN') {
        alert('This website is already opened in another tab');
        
    }
}