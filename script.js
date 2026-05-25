function toggleChatbot(){

  const chatbot = document.getElementById("chatbot");

  if(chatbot.style.display === "flex"){
    chatbot.style.display = "none";
  }else{
    chatbot.style.display = "flex";
  }
}

function sendMessage(){

  const input = document.getElementById("userInput");
  const chatBody = document.getElementById("chatBody");

  if(input.value.trim() === ""){
    return;
  }

  // USER MESSAGE

  const userMsg = document.createElement("div");
  userMsg.classList.add("user-message");
  userMsg.innerText = input.value;

  chatBody.appendChild(userMsg);

  // BOT REPLY

  const botMsg = document.createElement("div");
  botMsg.classList.add("bot-message");

  let reply = "Thanks for your message!";

  const message = input.value.toLowerCase();

  if(message.includes("solution")){
    reply = "We provide AI automation, smart analytics, and chatbot solutions.";
  }

  else if(message.includes("price")){
    reply = "Please contact our team for detailed pricing information.";
  }

  else if(message.includes("contact")){
    reply = "You can contact us at info@ai-solution.com";
  }

  else if(message.includes("help")){
    reply = "Sure! Tell me what kind of AI solution you need.";
  }

  setTimeout(() => {

    botMsg.innerText = reply;

    chatBody.appendChild(botMsg);

    chatBody.scrollTop = chatBody.scrollHeight;

  }, 500);

  input.value = "";

  chatBody.scrollTop = chatBody.scrollHeight;
}