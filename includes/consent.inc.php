<?php
// includes/consent.inc.php
// Minimal consent banner include. Uses /consent.php endpoint.
?>
<div id="consent-banner" style="display:none; position: fixed; bottom: 12px; left: 12px; right:12px; background:#fff; border:1px solid #ddd; padding:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); display:flex; gap:8px; align-items:center; z-index:9999;">
  <div style="flex:1">We use cookies to improve the site. Accept optional analytics?</div>
  <button id="consent-accept">Accept</button>
  <button id="consent-reject">Reject</button>
</div>
<script>
function __gb_getCookie(name) {
  const v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
  return v ? v.pop() : null;
}
if (__gb_getCookie('consent') === null) {
  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('consent-banner').style.display = 'flex';
  });
}
function __gb_postConsent(action){
  fetch('/consent.php', {
    method: 'POST',
    credentials: 'include',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({action})
  }).then(()=>{ document.getElementById('consent-banner').style.display='none'; });
}
document.addEventListener('click', function(e){
  if (e.target && e.target.id === 'consent-accept') __gb_postConsent('accept');
  if (e.target && e.target.id === 'consent-reject') __gb_postConsent('reject');
});
</script>
