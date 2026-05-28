// ========== NAVBAR SCROLL EFFECT ==========
window.addEventListener('scroll', () => {
  const header = document.querySelector('header');
  if (window.scrollY > 50) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

// ========== MOBILE MENU TOGGLE ==========
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navUl = document.querySelector('nav ul');

if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener('click', () => {
    navUl.classList.toggle('active');
    mobileMenuBtn.classList.toggle('active');
    
    // Animate hamburger to X
    const spans = mobileMenuBtn.querySelectorAll('span');
    if (mobileMenuBtn.classList.contains('active')) {
      spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
      spans[1].style.opacity = '0';
      spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    } else {
      spans[0].style.transform = 'none';
      spans[1].style.opacity = '1';
      spans[2].style.transform = 'none';
    }
  });
}

// ========== PAGE NAVIGATION LINKS ==========
// Get current page filename
const currentPage = window.location.pathname.split('/').pop() || 'index.html';

// Set active class on current page link
document.querySelectorAll('nav ul li a').forEach(link => {
  const linkHref = link.getAttribute('href');
  if (linkHref === currentPage) {
    link.classList.add('active');
  }
  
  // Add click handler for smooth navigation
  link.addEventListener('click', (e) => {
    // Close mobile menu if open
    if (navUl && navUl.classList.contains('active')) {
      navUl.classList.remove('active');
      if (mobileMenuBtn) {
        mobileMenuBtn.classList.remove('active');
        const spans = mobileMenuBtn.querySelectorAll('span');
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
      }
    }
  });
});

// ========== INTERNAL LINK HANDLING ==========
// Handle navigation for buttons and links that should navigate to other pages
document.querySelectorAll('[data-navigate]').forEach(element => {
  element.addEventListener('click', () => {
    const page = element.getAttribute('data-navigate');
    if (page) {
      window.location.href = page;
    }
  });
});

// Handle "Get Started", "Explore Solutions", "Contact Us" buttons
const exploreSolutionsBtn = document.querySelector('.btn-primary:contains("Explore Solutions")');
const contactUsBtn = document.querySelector('.btn-secondary:contains("Contact Us")');
const getInTouchBtn = document.querySelector('.btn-primary:contains("Get In Touch")');
const getStartedBtn = document.querySelector('.btn-primary:contains("Get Started")');
const startJourneyBtn = document.querySelector('.btn-primary:contains("Start Your Journey")');
const requestDemoBtn = document.querySelector('.btn-primary:contains("Request a Demo")');

// Helper function to check button text
function findButtonByText(text) {
  const buttons = document.querySelectorAll('.btn');
  for (let button of buttons) {
    if (button.textContent.includes(text)) {
      return button;
    }
  }
  return null;
}

// Add navigation to buttons
const exploreBtn = findButtonByText('Explore Solutions');
if (exploreBtn) {
  exploreBtn.addEventListener('click', () => {
    window.location.href = 'solutions.html';
  });
}

const contactBtn = findButtonByText('Contact Us');
if (contactBtn) {
  contactBtn.addEventListener('click', () => {
    window.location.href = 'contact.html';
  });
}

const getInTouchBtnElement = findButtonByText('Get In Touch');
if (getInTouchBtnElement) {
  getInTouchBtnElement.addEventListener('click', () => {
    window.location.href = 'contact.html';
  });
}

const getStartedBtnElement = findButtonByText('Get Started');
if (getStartedBtnElement) {
  getStartedBtnElement.addEventListener('click', () => {
    window.location.href = 'contact.html';
  });
}

const startJourneyBtnElement = findButtonByText('Start Your Journey');
if (startJourneyBtnElement) {
  startJourneyBtnElement.addEventListener('click', () => {
    window.location.href = 'contact.html';
  });
}

const requestDemoBtnElement = findButtonByText('Request a Demo');
if (requestDemoBtnElement) {
  requestDemoBtnElement.addEventListener('click', () => {
    window.location.href = 'contact.html';
  });
}

// Handle "Learn More" card links
document.querySelectorAll('.card-link, .learn-more, .read-more').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const href = link.getAttribute('href');
    if (href && href !== '#') {
      window.location.href = href;
    } else {
      // If no specific href, go to contact page
      window.location.href = 'contact.html';
    }
  });
});

// ========== SMOOTH SCROLLING FOR ANCHOR LINKS ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href !== '#' && href !== '#contactForm') {
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    } else if (href === '#contactForm') {
      e.preventDefault();
      const contactForm = document.getElementById('contactForm');
      if (contactForm) {
        contactForm.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
      }
    }
  });
});

// ========== ENHANCED CHATBOT FUNCTIONALITY ==========
// Chatbot state
let isTyping = false;
let messageQueue = [];

function toggleChatbot() {
  const chatbot = document.getElementById("chatbot");
  if (chatbot.style.display === "flex") {
    chatbot.style.display = "none";
  } else {
    chatbot.style.display = "flex";
    // Auto-scroll to bottom when opened
    const chatBody = document.getElementById("chatBody");
    if (chatBody) {
      setTimeout(() => {
        chatBody.scrollTop = chatBody.scrollHeight;
      }, 100);
    }
  }
}

function addMessage(text, isUser = false, isTypingIndicator = false) {
  const chatBody = document.getElementById("chatBody");
  const messageDiv = document.createElement("div");
  
  if (isUser) {
    messageDiv.classList.add("user-message");
    messageDiv.innerHTML = `
      <div class="message-content user-content">
        ${escapeHtml(text)}
      </div>
    `;
  } else if (isTypingIndicator) {
    messageDiv.classList.add("bot-message", "typing-indicator");
    messageDiv.innerHTML = `
      <div class="bot-avatar">
        <i class="fas fa-robot"></i>
      </div>
      <div class="message-content typing">
        <span></span><span></span><span></span>
      </div>
    `;
  } else {
    messageDiv.classList.add("bot-message");
    messageDiv.innerHTML = `
      <div class="bot-avatar">
        <i class="fas fa-robot"></i>
      </div>
      <div class="message-content">
        ${escapeHtml(text)}
      </div>
    `;
  }
  
  chatBody.appendChild(messageDiv);
  chatBody.scrollTop = chatBody.scrollHeight;
  return messageDiv;
}

// Helper function to escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Enhanced bot response logic with navigation suggestions
function getBotResponse(message) {
  const lowerMsg = message.toLowerCase();
  
  // Greetings
  if (lowerMsg.match(/^(hello|hi|hey|greetings|good morning|good afternoon)/)) {
    return "Hello! 👋 Welcome to AI Solutions. How can I assist you today?";
  }
  
  // Solutions/Services with navigation
  if (lowerMsg.includes("solution") || lowerMsg.includes("service") || lowerMsg.includes("offer")) {
    return "We offer several AI-powered solutions:\n\n• 🤖 **AI Automation** - Automate repetitive tasks\n• 📊 **Smart Analytics** - Data-driven insights\n• 💬 **AI Chatbots** - 24/7 customer support\n• ☁️ **Cloud AI** - Scalable cloud solutions\n\nWhich one would you like to learn more about? You can also visit our [Solutions page](solutions.html) for more details.";
  }
  
  // Specific solution inquiries with navigation
  if (lowerMsg.includes("automation")) {
    return "Our AI Automation solution helps businesses streamline workflows, reduce manual tasks, and increase efficiency by up to 40%. Would you like to schedule a demo? [Learn more about AI Automation](solutions.html)";
  }
  
  if (lowerMsg.includes("analytics")) {
    return "Smart Analytics provides real-time insights, predictive modeling, and data visualization to help you make informed business decisions. Interested in a free consultation? [Explore Smart Analytics](solutions.html)";
  }
  
  if (lowerMsg.includes("chatbot")) {
    return "Our AI Chatbots offer natural language processing, 24/7 availability, and seamless integration with your existing systems. They can handle up to 80% of common customer queries automatically! [See Chatbot Solutions](solutions.html)";
  }
  
  if (lowerMsg.includes("cloud")) {
    return "Cloud AI provides scalable, secure, and flexible AI deployment options. We support major cloud providers including AWS, Azure, and Google Cloud. Need specific requirements? [Explore Cloud AI](solutions.html)";
  }
  
  // Pricing/Cost
  if (lowerMsg.includes("price") || lowerMsg.includes("cost") || lowerMsg.includes("pricing")) {
    return "Our pricing is customized based on your specific needs and scale. I'd recommend scheduling a consultation with our sales team for an accurate quote. Would you like me to connect you with them? [Contact our sales team](contact.html)";
  }
  
  // Contact information with navigation
  if (lowerMsg.includes("contact") || lowerMsg.includes("email") || lowerMsg.includes("phone") || lowerMsg.includes("reach")) {
    return "You can reach us through:\n\n📧 **Email:** info@ai-solution.com\n📞 **Phone:** +1 (555) 123-4567\n📍 **Address:** 123 AI Boulevard, Sunderland, UK\n\nOr [fill out our contact form](contact.html) and we'll get back to you within 24 hours!";
  }
  
  // Demo request with navigation
  if (lowerMsg.includes("demo") || lowerMsg.includes("see it") || lowerMsg.includes("show me")) {
    return "I'd be happy to help you schedule a demo! 🎯\n\nPlease [visit our contact page](contact.html) or provide your email address here, and our team will contact you within 24 hours to arrange a personalized demo of our AI solutions.";
  }
  
  // About company
  if (lowerMsg.includes("about") || lowerMsg.includes("company") || lowerMsg.includes("who are you")) {
    return "AI Solutions is a leading provider of artificial intelligence solutions for modern workplaces. Founded in 2020, we help businesses transform their operations through innovative AI technology. Our mission is to make AI accessible and valuable for organizations of all sizes. [Learn more about us](index.html)";
  }
  
  // Gallery/Showcase
  if (lowerMsg.includes("gallery") || lowerMsg.includes("images") || lowerMsg.includes("photos")) {
    return "Want to see our AI technology in action? Check out our [Gallery page](gallery.html) for images of our solutions, events, and team! 📸";
  }
  
  // Insights/Blog
  if (lowerMsg.includes("insights") || lowerMsg.includes("blog") || lowerMsg.includes("articles")) {
    return "Stay updated with the latest AI trends and insights! Visit our [Insights page](insights.html) for articles, news, and expert analysis on artificial intelligence. 📚";
  }
  
  // Testimonials
  if (lowerMsg.includes("testimonials") || lowerMsg.includes("review") || lowerMsg.includes("client say")) {
    return "Don't just take our word for it! Read what our clients say about their experience with AI Solutions. [View testimonials](testimonials.html) ⭐";
  }
  
  // Support/Help
  if (lowerMsg.includes("help") || lowerMsg.includes("support")) {
    return "I'm here to help! You can ask me about:\n\n• Our AI solutions and services\n• Pricing and plans\n• Scheduling a demo\n• Contact information\n• Company information\n\nWhat would you like to know? Or [contact our support team](contact.html) for immediate assistance.";
  }
  
  // Thank you
  if (lowerMsg.includes("thank")) {
    return "You're very welcome! 😊 Is there anything else I can help you with? Feel free to [explore our website](index.html) for more information.";
  }
  
  // Goodbye
  if (lowerMsg.includes("bye") || lowerMsg.includes("goodbye")) {
    return "Thank you for chatting with us! Have a great day! 👋 Feel free to come back if you have more questions. [Return to homepage](index.html)";
  }
  
  // Default response with helpful links
  return "Thank you for your message! 💬 I'd be happy to help. You can ask me about:\n\n• Our AI solutions ([Solutions](solutions.html))\n• Pricing and demos\n• Contact information ([Contact](contact.html))\n• Company info ([Home](index.html))\n• Client success stories ([Testimonials](testimonials.html))\n\nWhat would you like to know more about?";
}

// Send message with typing indicator
async function sendMessage() {
  const input = document.getElementById("userInput");
  const message = input.value.trim();
  
  if (message === "" || isTyping) return;
  
  // Add user message
  addMessage(message, true);
  input.value = "";
  
  // Show typing indicator
  isTyping = true;
  const typingIndicator = addMessage("", false, true);
  
  // Get bot response (simulate thinking time)
  setTimeout(() => {
    // Remove typing indicator
    typingIndicator.remove();
    
    // Get and add bot response with markdown-style links converted to HTML
    let response = getBotResponse(message);
    // Convert [text](url) to HTML links that open in same tab
    response = response.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="chat-link">$1</a>');
    addMessage(response, false);
    
    isTyping = false;
    
    // Process next message in queue if any
    if (messageQueue.length > 0) {
      const nextMsg = messageQueue.shift();
      document.getElementById("userInput").value = nextMsg;
      sendMessage();
    }
  }, 600 + Math.random() * 400);
}

// Queue message if bot is typing
function queueMessage(message) {
  if (isTyping) {
    messageQueue.push(message);
    showQueuedNotification();
  } else {
    document.getElementById("userInput").value = message;
    sendMessage();
  }
}

// Show notification when message is queued
function showQueuedNotification() {
  const chatBody = document.getElementById("chatBody");
  const notification = document.createElement("div");
  notification.classList.add("system-message");
  notification.innerHTML = `
    <div class="system-content">
      <i class="fas fa-clock"></i> Message queued - I'll respond shortly
    </div>
  `;
  chatBody.appendChild(notification);
  setTimeout(() => notification.remove(), 2000);
}

// Allow Enter key to send message
document.addEventListener("DOMContentLoaded", function() {
  const input = document.getElementById("userInput");
  if (input) {
    input.addEventListener("keypress", function(e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  }
});

// ========== SCROLL REVEAL ANIMATIONS ==========
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

// Observe all sections for fade-in
document.querySelectorAll('.hero-text, .hero-image, .mission, .feature-card, .solution-card, .insight-card, .testimonial-card, .gallery-item, .info-card, .stat-card').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = 'all 0.6s ease';
  observer.observe(el);
});

// ========== SUGGESTED QUESTIONS FOR CHATBOT ==========
// Add suggested questions when chatbot is idle
function addSuggestedQuestions() {
  const chatBody = document.getElementById("chatBody");
  // Check if suggestions already exist
  if (document.querySelector('.suggested-questions')) return;
  
  const suggestions = document.createElement("div");
  suggestions.classList.add("suggested-questions");
  suggestions.innerHTML = `
    <div class="suggestions-title">💡 Suggested Questions:</div>
    <div class="suggestions-list">
      <button class="suggestion-btn" onclick="queueMessage('What solutions do you offer?')">🔧 What solutions do you offer?</button>
      <button class="suggestion-btn" onclick="queueMessage('How much does it cost?')">💰 How much does it cost?</button>
      <button class="suggestion-btn" onclick="queueMessage('Can I see a demo?')">🎥 Can I see a demo?</button>
      <button class="suggestion-btn" onclick="queueMessage('How can I contact you?')">📞 How can I contact you?</button>
      <button class="suggestion-btn" onclick="queueMessage('Show me testimonials')">⭐ Show me testimonials</button>
      <button class="suggestion-btn" onclick="queueMessage('View gallery')">🖼️ View gallery</button>
    </div>
  `;
  chatBody.appendChild(suggestions);
}

// Check if chatbot is empty and add suggestions
setTimeout(() => {
  const chatBody = document.getElementById("chatBody");
  if (chatBody && chatBody.children.length <= 1) {
    addSuggestedQuestions();
  }
}, 1000);