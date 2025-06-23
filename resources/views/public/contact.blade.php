@extends('layoutspublic.app')

@section('content')
    <!-- Map Begin -->
    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d111551.9926412813!2d-90.27317134641879!3d38.606612219170856!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sbd!4v1597926938024!5m2!1sen!2sbd" height="500" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
    <!-- Map End -->

    <!-- Contact Section Begin -->
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Information</span>
                            <h2>Contact Us</h2>
                            <p>Hubungi kami melalui WhatsApp untuk pertanyaan lebih lanjut.</p>
                        </div>
                        <ul>
                            <li>
                                <h4>WhatsApp</h4>
                                <p><a href="https://wa.me/6289661770123" target="_blank" class="whatsapp-link">+62 812-3456-7890</a></p>
                            </li>
                            <li>
                                <h4>Email</h4>
                                <p>julianarwansahh@gmail.com</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="contact__form">
                        <form id="whatsappForm">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" id="name" placeholder="Your Name" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" id="phone" placeholder="Your WhatsApp Number" required>
                                </div>
                                <div class="col-lg-12">
                                    <textarea id="message" placeholder="Your Message" required></textarea>
                                    <button type="button" onclick="sendToWhatsApp()" class="site-btn">Send via WhatsApp</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <script>
        function sendToWhatsApp() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            
            // Format nomor WhatsApp (hilangkan karakter selain angka)
            const formattedPhone = phone.replace(/\D/g, '');
            
            // Nomor WhatsApp tujuan (ganti dengan nomor Anda)
            const targetPhone = '6289661770123';
            
            // Pesan yang akan dikirim
            const encodedMessage = encodeURIComponent(
                `Hello, my name is ${name}.\n\n${message}\n\nPlease contact me back at: ${phone}`
            );
            
            // URL WhatsApp
            const whatsappUrl = `https://wa.me/${targetPhone}?text=${encodedMessage}`;
            
            // Buka WhatsApp
            window.open(whatsappUrl, '_blank');
            
            // Reset form setelah pengiriman
            document.getElementById('whatsappForm').reset();
        }
    </script>

    <style>
        .whatsapp-link {
            color: #25D366;
            font-weight: bold;
            text-decoration: none;
        }
        .whatsapp-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection