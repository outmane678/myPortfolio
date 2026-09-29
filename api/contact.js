const nodemailer = require("nodemailer")

const esc = (s = "") =>
  String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]))

module.exports = async (req, res) => {
  res.setHeader("Content-Type", "text/plain")
  if (req.method !== "POST") return res.status(405).send("error")

  const { name, email, phone, message, "g-recaptcha-response": token } = req.body || {}
  if (!name || !email || !phone || !message) return res.status(400).send("error")

  try {
    const g = await fetch("https://www.google.com/recaptcha/api/siteverify", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ secret: process.env.RECAPTCHA_SECRET, response: token || "" }),
    }).then((r) => r.json())
    if (!g.success) return res.status(400).send("InvalidCaptcha")

    const transporter = nodemailer.createTransport({
      service: "gmail",
      auth: { user: process.env.MAIL_USER, pass: process.env.MAIL_PASS },
    })

    await transporter.sendMail({
      from: `"Portfolio" <${process.env.MAIL_USER}>`,
      to: "otmanflow88@gmail.com",
      replyTo: email,
      subject: "New Contact Form Submission",
      html: `<b>Name:</b> ${esc(name)}<br><b>Email:</b> ${esc(email)}<br><b>Phone:</b> ${esc(phone)}<br><b>Message:</b> ${esc(message)}`,
    })
    await transporter.sendMail({
      from: `"Outmane Elkairi" <${process.env.MAIL_USER}>`,
      to: email,
      subject: "Thank you for contacting me!",
      html: `Hello ${esc(name)},<br>Thank you for contacting me. I will get back to you as soon as possible.<br><br>Best regards,<br>Outmane Elkairi`,
    })
    res.status(200).send("success")
  } catch (e) {
    console.error(e)
    res.status(500).send("error")
  }
}
