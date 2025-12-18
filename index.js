import express from "express";
import fetch from "node-fetch";
import fs from "fs";

const app = express();
app.use(express.json());

const BOT_TOKEN = process.env.BOT_TOKEN;
const ADMIN_ID  = process.env.ADMIN_ID;
const API = `https://api.telegram.org/bot${BOT_TOKEN}`;
const VIP_FILE = "./vip_users.json";

function loadVIP() {
  return fs.existsSync(VIP_FILE)
    ? JSON.parse(fs.readFileSync(VIP_FILE))
    : {};
}

function saveVIP(vip) {
  fs.writeFileSync(VIP_FILE, JSON.stringify(vip));
}

function sendMessage(chat_id, text) {
  fetch(`${API}/sendMessage`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ chat_id, text })
  });
}

app.post("/", (req, res) => {
  const msg = req.body.message;
  if (!msg) return res.send("ok");

  const chat_id = msg.chat.id;
  const text = msg.text || "";
  let vip = loadVIP();

  // auto-expire
  for (let u in vip) {
    if (vip[u] < Date.now()) delete vip[u];
  }
  saveVIP(vip);

  if (text === "/start") {
    sendMessage(chat_id, "🤖 Bot Active\nUse /vip");
  }

  else if (text === "/vip") {
    if (vip[chat_id]) {
      sendMessage(chat_id, "✅ You are VIP");
    } else {
      sendMessage(chat_id, "❌ You are not VIP");
    }
  }

  else if (text.startsWith("/addvip") && chat_id == ADMIN_ID) {
    const p = text.split(" ");
    vip[p[1]] = Date.now() + p[2] * 86400000;
    saveVIP(vip);
    sendMessage(chat_id, "✅ VIP Added");
  }

  res.send("ok");
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log("Bot running"));
