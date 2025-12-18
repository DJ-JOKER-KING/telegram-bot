<?php
echo "Railway PHP OK";
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VIP XCYBER71</title>

<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">

<style>
html,body{margin:0;height:100%;font-family:'Rajdhani',sans-serif;background:#000;overflow:hidden}
iframe{width:100%;height:100vh;border:none}
.lock{position:fixed;inset:0;background:radial-gradient(circle,#1a1300,#000 65%);display:flex;flex-direction:column;justify-content:center;align-items:center;color:#FFD700;z-index:10000}
.crown-top{font-size:44px;text-shadow:0 0 18px gold;animation:float 2.5s infinite}
@keyframes float{50%{transform:translateY(-6px)}}
.lock-logo{width:130px;margin:12px 0 16px;filter:drop-shadow(0 0 25px gold)}
.lock input{width:240px;padding:14px;background:#0b0b0b;border:1.5px solid #c77dff;border-radius:14px;color:#ffd700;text-align:center}
.lock button{margin-top:14px;padding:12px 36px;border:none;border-radius:14px;background:linear-gradient(145deg,#ffd700,#caa84a);font-weight:700}
.err{color:#ff4d4d;margin-top:6px}
.overlay{position:fixed;top:120px;right:20px;width:260px;background:#0f0f0f;border:1.5px solid #c77dff;border-radius:18px;padding:14px;display:none;z-index:9999}
.signal{margin-top:8px;font-size:28px;text-align:center;font-weight:700}
.big{color:#ff4d4d}.small{color:#4dff9d}
.timer{text-align:center;color:#ccc}
.btn{display:block;margin-top:12px;padding:10px;border-radius:10px;text-align:center;background:linear-gradient(145deg,#ffd700,#caa84a);color:#000;text-decoration:none}
</style>
</head>

<body>

<iframe src="https://dkwin15.com/#/register?invitationCode=78864930641"></iframe>

<div class="lock" id="lock">
  <div class="crown-top">👑</div>
  <img src="https://i.postimg.cc/pdkMfdtb/Background-Eraser-20251216-222812295.png" class="lock-logo">
  <h2>VIP XCYBER71 ACCESS</h2>
  <button onclick="unlock()">Unlock VIP</button>
  <div class="err" id="err"></div>
</div>

<div class="overlay" id="overlay">
  <div class="signal" id="signal">--</div>
  <div class="timer" id="timer">--</div>
  <a class="btn" href="https://t.me/dkwin_with_sabbir" target="_blank">Join Telegram</a>
</div>

<script>
function getDeviceID(){
  let id = localStorage.getItem("VIP_DEVICE_ID");
  if(!id){
    id = btoa(navigator.userAgent + screen.width + screen.height).substring(0,16);
    localStorage.setItem("VIP_DEVICE_ID", id);
  }
  return id;
}

function unlock(){
  let user = prompt("Telegram username (@ ছাড়া)");
  if(!user) return;

  fetch("/bot.php",{
    method:"POST",
    headers:{ "Content-Type":"application/json" },
    body:JSON.stringify({ user:user, device:getDeviceID() })
  })
  .then(r=>r.json())
  .then(d=>{
    if(d.status==="ok"){
      lock.style.display="none";
      overlay.style.display="block";
    }else{
      err.innerText=d.msg;
    }
  });
}

function generateSignal(){
  let big=Math.random()<0.5;
  let n=big?Math.floor(Math.random()*5)+5:Math.floor(Math.random()*5);
  signal.innerHTML=(big?"BIG":"SMALL")+"<br>"+n;
  signal.className="signal "+(big?"big":"small");
}

setInterval(()=>{
  generateSignal();
  timer.innerText="Next update...";
},30000);
</script>

</body>
</html>
