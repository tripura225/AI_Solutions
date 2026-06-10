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
/* const exploreSolutionsBtn = document.querySelector('.btn-primary:contains("Explore Solutions")');
const contactUsBtn = document.querySelector('.btn-secondary:contains("Contact Us")');
const getInTouchBtn = document.querySelector('.btn-primary:contains("Get In Touch")');
const getStartedBtn = document.querySelector('.btn-primary:contains("Get Started")');
const startJourneyBtn = document.querySelector('.btn-primary:contains("Start Your Journey")');
const requestDemoBtn = document.querySelector('.btn-primary:contains("Request a Demo")');*/

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

// ========== CHATBOT FUNCTIONALITY ==========

let isTyping = false;
let messageQueue = [];

function toggleChatbot() {
  const chatbot = document.getElementById("chatbot");
  if (chatbot.style.display === "flex") {
    chatbot.style.display = "none";
  } else {
    chatbot.style.display = "flex";
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
  if (!chatBody) return;
  
  const messageDiv = document.createElement("div");
  
  if (isUser) {
    messageDiv.classList.add("user-message");
    messageDiv.innerHTML = `<div class="message-content user-content">${escapeHtml(text)}</div>`;
  } else if (isTypingIndicator) {
    messageDiv.classList.add("bot-message", "typing-indicator");
    messageDiv.innerHTML = `
      <div class="bot-avatar"><i class="fas fa-robot"></i></div>
      <div class="message-content typing"><span></span><span></span><span></span></div>
    `;
  } else {
    messageDiv.classList.add("bot-message");
    messageDiv.innerHTML = `
      <div class="bot-avatar"><i class="fas fa-robot"></i></div>
      <div class="message-content">${escapeHtml(text)}</div>
    `;
  }
  
  chatBody.appendChild(messageDiv);
  chatBody.scrollTop = chatBody.scrollHeight;
  return messageDiv;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function getBotResponse(message) {
  const lowerMsg = message.toLowerCase();
  
  if (lowerMsg.match(/^(hello|hi|hey|greetings)/)) {
    return "Hello! 👋 Welcome to AI Solutions. How can I assist you today?";
  }
  if (lowerMsg.includes("solution") || lowerMsg.includes("service") || lowerMsg.includes("offer")) {
    return "We offer:\n\n• 🤖 AI Automation\n• 📊 Smart Analytics\n• 💬 AI Chatbots\n• ☁️ Cloud AI\n\nWhich one interests you?";
  }
  if (lowerMsg.includes("automation")) {
    return "AI Automation helps streamline workflows and reduce manual tasks by up to 40%. Would you like a demo?";
  }
  if (lowerMsg.includes("analytics")) {
    return "Smart Analytics provides real-time insights and predictive modeling to help you make data-driven decisions.";
  }
  if (lowerMsg.includes("chatbot")) {
    return "Our AI Chatbots offer 24/7 customer support with natural language understanding.";
  }
  if (lowerMsg.includes("cloud")) {
    return "Cloud AI provides scalable, secure AI deployment on AWS, Azure, and Google Cloud.";
  }
  if (lowerMsg.includes("price") || lowerMsg.includes("cost")) {
    return "Our pricing is customized based on your needs. Please contact our sales team for a quote!";
  }
  if (lowerMsg.includes("contact") || lowerMsg.includes("email")) {
    return "Email us at info@ai-solution.com or call +1 (555) 123-4567";
  }
  if (lowerMsg.includes("demo")) {
    return "I'd be happy to schedule a demo! Please visit our Contact page.";
  }
  if (lowerMsg.includes("thank")) {
    return "You're welcome! 😊 Anything else I can help with?";
  }
  if (lowerMsg.includes("bye")) {
    return "Thank you for chatting! Have a great day! 👋";
  }
  return "Thank you for your message! 💬 How can I help you? You can ask about our solutions, pricing, demos, or contact information.";
}

// MAIN SEND MESSAGE FUNCTION
// Add this variable at the top
let userEmail = null;

// Modify sendMessage function
async function sendMessage() {
    const input = document.getElementById("userInput");
    const message = input.value.trim();
    
    if (message === "" || isTyping) return;
    
    // If email not collected, ask for it
    if (!userEmail && !message.includes("@")) {
        addMessage("Please provide your email address so we can respond to you if needed:", false);
        userEmail = "waiting";
        input.value = "";
        return;
    }
    
    if (userEmail === "waiting" && message.includes("@")) {
        userEmail = message;
        addMessage("Thank you! Your email has been saved. Now, how can I help you?", false);
        input.value = "";
        return;
    }
    
    // Regular chat flow continues...
    addMessage(message, true);
    input.value = "";
    
    isTyping = true;
    const typingIndicator = addMessage("", false, true);
    
    setTimeout(async () => {
        if (typingIndicator && typingIndicator.remove) typingIndicator.remove();
        const response = getBotResponse(message);
        addMessage(response, false);
        isTyping = false;
        
        // Save to database with email
        try {
            const chatData = {
                message: message,
                response: response.replace(/<[^>]*>/g, ''),
                email: userEmail && userEmail !== "waiting" ? userEmail : null
            };
            
            const fetchResponse = await fetch('backend/api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(chatData)
            });
            
            const result = await fetchResponse.json();
            if (result.success) {
                console.log('✅ Chat saved to database');
            }
        } catch (error) {
            console.log('❌ Error saving chat:', error);
        }
        
        if (messageQueue.length > 0) {
            const nextMsg = messageQueue.shift();
            document.getElementById("userInput").value = nextMsg;
            sendMessage();
        }
    }, 800);
}


// Enter key support
document.addEventListener("DOMContentLoaded", function() {
  const input = document.getElementById("userInput");
  if (input) {
    input.addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
      }
    });
  }
});

function queueMessage(message) {
  if (isTyping) {
    messageQueue.push(message);
  } else {
    document.getElementById("userInput").value = message;
    sendMessage();
  }
}

// Add suggested questions (ONLY ONCE)
function addSuggestedQuestions() {
  const chatBody = document.getElementById("chatBody");
  if (!chatBody) return;
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
    </div>
  `;
  chatBody.appendChild(suggestions);
}

// Add suggestions after load
setTimeout(() => {
  const chatBody = document.getElementById("chatBody");
  if (chatBody && chatBody.children.length <= 2) {
    addSuggestedQuestions();
  }
}, 500);

// Make functions global
window.sendMessage = sendMessage;
window.queueMessage = queueMessage;
window.toggleChatbot = toggleChatbot;

console.log('✅ Chatbot ready!');



// ========== BACKEND API INTEGRATION ==========

// ========== CONTACT FORM - REQUIRE ALL FIELDS ==========

const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Get all form values
        const fullName = document.getElementById('fullName')?.value.trim() || '';
        const email = document.getElementById('email')?.value.trim() || '';
        const phone = document.getElementById('phone')?.value.trim() || '';
        const company = document.getElementById('company')?.value.trim() || '';
        const service = document.getElementById('service')?.value || '';
        const message = document.getElementById('message')?.value.trim() || '';
        
        // Clear previous errors
        clearAllErrors();
        
        // Validation array - Track all errors
        let errors = [];
        
        // 1. Validate Full Name (REQUIRED)
        if (fullName === '') {
            errors.push({ field: 'fullName', message: 'Full name is required' });
        } else if (fullName.length < 2) {
            errors.push({ field: 'fullName', message: 'Name must be at least 2 characters' });
        } else if (fullName.length > 50) {
            errors.push({ field: 'fullName', message: 'Name must be less than 50 characters' });
        }
        
        // 2. Validate Email (REQUIRED)
        if (email === '') {
            errors.push({ field: 'email', message: 'Email address is required' });
        } else if (!isValidEmail(email)) {
            errors.push({ field: 'email', message: 'Please enter a valid email address (e.g., name@example.com)' });
        }
        
        // 3. Validate Phone (REQUIRED)
        if (phone === '') {
            errors.push({ field: 'phone', message: 'Phone number is required' });
        } else if (!isValidPhone(phone)) {
            errors.push({ field: 'phone', message: 'Please enter a valid phone number (e.g., +1 555 123 4567)' });
        }
        
        // 4. Validate Company (REQUIRED)
        if (company === '') {
            errors.push({ field: 'company', message: 'Company name is required' });
        } else if (company.length < 2) {
            errors.push({ field: 'company', message: 'Company name must be at least 2 characters' });
        }
        
        // 5. Validate Service Selection (REQUIRED)
        if (service === '' || service === 'Select Service Interest') {
            errors.push({ field: 'service', message: 'Please select a service interest' });
        }
        
        // 6. Validate Message (REQUIRED)
        if (message === '') {
            errors.push({ field: 'message', message: 'Message is required' });
        } else if (message.length < 10) {
            errors.push({ field: 'message', message: 'Message must be at least 10 characters' });
        } else if (message.length > 1000) {
            errors.push({ field: 'message', message: 'Message must be less than 1000 characters' });
        }
        
        // If there are errors, show them and STOP submission
        if (errors.length > 0) {
            showAllErrors(errors);
            // Scroll to first error
            const firstErrorField = document.getElementById(errors[0].field);
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return; // IMPORTANT: Stop form submission here
        }
        
        // If no errors, submit the form
        try {
            // Show loading state
            const submitBtn = document.querySelector('#contactForm .submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            const formData = {
                full_name: fullName,
                email: email,
                phone: phone,
                company: company,
                service: service,
                message: message
            };
            
            const response = await fetch('backend/api/contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (result.success) {
                showFormMessage(result.message, 'success');
                contactForm.reset(); // Clear all fields
                // Clear any remaining errors
                clearAllErrors();
            } else {
                showFormMessage(result.message, 'error');
            }
        } catch (error) {
            showFormMessage('Network error. Please check your connection and try again.', 'error');
            // Reset button
            const submitBtn = document.querySelector('#contactForm .submit-btn');
            if (submitBtn) {
                submitBtn.innerHTML = 'Send Message <i class="fas fa-paper-plane"></i>';
                submitBtn.disabled = false;
            }
        }
    });
}

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
}

// Helper function to validate phone
function isValidPhone(phone) {
    // Allows: +1 555 123 4567, 555-123-4567, 5551234567, (555) 123-4567
    const phoneRegex = /^[\d\s\+\(\)\-]{10,20}$/;
    return phoneRegex.test(phone);
}

// Function to show all errors
function showAllErrors(errors) {
    errors.forEach(error => {
        const field = document.getElementById(error.field);
        if (field) {
            // Highlight field in red
            field.style.borderColor = '#e74c3c';
            field.style.borderWidth = '2px';
            field.style.borderStyle = 'solid';
            
            // Remove existing error message for this field
            const existingError = field.parentElement.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create new error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = error.message;
            errorDiv.style.color = '#e74c3c';
            errorDiv.style.fontSize = '0.7rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.style.paddingLeft = '0.5rem';
            field.parentElement.appendChild(errorDiv);
        }
    });
}

// Function to clear all errors
function clearAllErrors() {
    // Clear border colors from all inputs
    const inputs = document.querySelectorAll('#contactForm input, #contactForm select, #contactForm textarea');
    inputs.forEach(input => {
        input.style.borderColor = '';
        input.style.borderWidth = '';
        input.style.borderStyle = '';
    });
    
    // Clear all error message divs
    const errorMessages = document.querySelectorAll('.field-error');
    errorMessages.forEach(msg => msg.remove());
}

// Real-time validation - clear error when user starts typing
document.querySelectorAll('#contactForm input, #contactForm select, #contactForm textarea').forEach(field => {
    field.addEventListener('input', function() {
        // Clear error for this specific field
        this.style.borderColor = '';
        const errorDiv = this.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    });
    
    // Also clear on focus
    field.addEventListener('focus', function() {
        this.style.borderColor = '';
        const errorDiv = this.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    });
});

// Function to show form-level message
function showFormMessage(message, type) {
    const successMsg = document.getElementById('formSuccess');
    if (successMsg) {
        successMsg.textContent = message;
        successMsg.className = 'form-success ' + type;
        successMsg.style.display = 'block';
        
        // Scroll to message
        successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Hide after 5 seconds
        setTimeout(() => {
            successMsg.style.display = 'none';
        }, 5000);
    }
}



// ========== NEWSLETTER SUBSCRIPTION ==========
async function subscribeNewsletter() {
    const emailInput = document.getElementById('newsletterEmail');
    const email = emailInput?.value.trim();
    
    // Validate email
    if (!email) {
        showAlert('error', 'Email Required', 'Please enter your email address.');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('error', 'Invalid Email', 'Please enter a valid email address.');
        return;
    }
    
    // Show loading state
    const subscribeBtn = document.querySelector('#newsletterSection .btn-primary, .newsletter-form .btn-primary');
    const originalText = subscribeBtn?.innerHTML;
    if (subscribeBtn) {
        subscribeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        subscribeBtn.disabled = true;
    }
    
    try {
        const response = await fetch('backend/api/newsletter.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        });
        
        const result = await response.json();
        
        // Reset button
        if (subscribeBtn) {
            subscribeBtn.innerHTML = originalText;
            subscribeBtn.disabled = false;
        }
        
        if (result.success) {
            showAlert('success', 'Subscribed!', result.message);
            emailInput.value = ''; // Clear input field
        } else {
            showAlert('error', 'Subscription Failed', result.message);
        }
    } catch (error) {
        // Reset button
        if (subscribeBtn) {
            subscribeBtn.innerHTML = originalText;
            subscribeBtn.disabled = false;
        }
        showAlert('error', 'Connection Error', 'Unable to connect. Please try again later.');
    }
}

// Alert function (if you don't have it already)
function showAlert(type, title, message) {
    // Remove existing alert
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert custom-alert-${type}`;
    
    let icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
    
    alertDiv.innerHTML = `
        <div class="alert-content">
            <div class="alert-icon">${icon}</div>
            <div class="alert-text">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv) {
            alertDiv.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

// ========== DEBUG: CHECK IF SENDMESSAGE EXISTS ==========
console.log('Script loaded successfully');
console.log('sendMessage function exists:', typeof sendMessage === 'function');

// Test function to verify button click
function testChat() {
    console.log('Test chat function called');
    const input = document.getElementById('userInput');
    if (input) {
        console.log('Input found, value:', input.value);
    } else {
        console.log('Input not found!');
    }
}

// Make sure sendMessage is globally available
window.sendMessage = sendMessage;
window.queueMessage = queueMessage;

console.log('Chatbot functions are ready!');


// ========== FOOTER CONTACT INTERACTIONS ==========

// Open Google Maps
function openMap() {
  const address = encodeURIComponent("Sunderland, UK");
  window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
}

// Make a phone call
function makeCall() {
  const phoneNumber = "+15551234567";
  if (confirm(`Call ${phoneNumber}?`)) {
    window.location.href = `tel:${phoneNumber}`;
  }
}

// Send email
function sendEmail() {
  const email = "info@ai-solution.com";
  window.location.href = `mailto:${email}?subject=AI Solutions Inquiry&body=Hello, I would like to learn more about your AI solutions.`;
}