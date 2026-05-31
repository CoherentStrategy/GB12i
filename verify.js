const nodemailer = require('nodemailer');

async function sendVerificationEmail(userEmail) {
  // 1. Generate a random 6-digit verification code
  const verificationCode = Math.floor(100000 + Math.random() * 900000);

  // 2. Create a temporary test email server (for development)
  let testAccount = await nodemailer.createTestAccount();

  let transporter = nodemailer.createTransport({
    host: "smtp.ethereal.email",
    port: 587,
    secure: false, 
    auth: {
      user: testAccount.user, 
      pass: testAccount.pass, 
    },
  });

  // 3. Set up email details
  let mailOptions = {
    from: '"My Secure App" <no-reply@example.com>',
    to: userEmail,
    subject: "Verify your account",
    text: `Your verification code is: ${verificationCode}`,
    html: `<b>Your verification code is: ${verificationCode}</b>`,
  };

  // 4. Send the email
  let info = await transporter.sendMail(mailOptions);

  console.log("Email sent successfully!");
  // Preview URL only works when using temporary Ethereal accounts
  console.log("Preview URL to view the inbox: %s", nodemailer.getTestMessageUrl(info));
  
  return verificationCode; 
}

// Test the function
// sendVerificationEmail("testuser@example.com");
