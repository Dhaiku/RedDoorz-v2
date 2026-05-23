<!-- ===== FOOTER ===== -->
<footer class="footer-rd">
    <div style="background:#0E0C0C; padding: 48px 0 0;">
        <div class="container">
            <div class="row g-4 pb-4" style="border-bottom: 1px solid rgba(255,255,255,0.08);">

                <!-- Brand column -->
                <div class="col-lg-4">
                    <a href="/index.php" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; margin-bottom:14px;">
                        <span style="background:rgba(184,0,32,0.25); border:1px solid rgba(184,0,32,0.4); border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; font-size:17px; color:#fff;">
                            <i class="bi bi-door-open-fill"></i>
                        </span>
                        <span style="font-size:20px; font-weight:700; color:#fff; letter-spacing:-0.3px;">RedDoorz</span>
                    </a>
                    <p style="font-size:13px; color:rgba(255,255,255,0.45); line-height:1.7; margin:0 0 18px; max-width:280px;">
                        Affordable, clean, and comfortable hotel stays across the Philippines, Indonesia, Singapore, and Vietnam.
                    </p>
                    <div style="display:flex; gap:10px;">
                        <?php foreach([
                            ['bi-facebook',  '#'],
                            ['bi-instagram', '#'],
                            ['bi-twitter-x', '#'],
                        ] as [$ic, $url]): ?>
                        <a href="<?= $url ?>" style="width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.1); display:inline-flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.55); font-size:14px; transition:all 0.18s;"
                           onmouseover="this.style.background='rgba(184,0,32,0.35)';this.style.color='#fff';"
                           onmouseout="this.style.background='rgba(255,255,255,0.07)';this.style.color='rgba(255,255,255,0.55)';">
                            <i class="bi <?= $ic ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-sm-6 col-lg-2">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:14px;">Explore</div>
                    <?php foreach([
                        ['Find Hotels',  '/hotels/search.php'],
                        ['Featured',     '/index.php#featured'],
                        ['Philippines',  '/hotels/search.php?city=Manila'],
                        ['Indonesia',    '/hotels/search.php?city=Bali'],
                        ['Singapore',    '/hotels/search.php?city=Singapore'],
                    ] as [$lbl, $href]): ?>
                    <a href="<?= $href ?>" style="display:block; font-size:13px; color:rgba(255,255,255,0.5); margin-bottom:8px; text-decoration:none; transition:color 0.15s;"
                       onmouseover="this.style.color='#fff';" onmouseout="this.style.color='rgba(255,255,255,0.5)';">
                        <?= $lbl ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Account -->
                <div class="col-sm-6 col-lg-2">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:14px;">Account</div>
                    <?php foreach([
                        ['Login',        '/auth/login.php'],
                        ['Register',     '/auth/register.php'],
                        ['My Bookings',  '/customer/dashboard.php'],
                        ['My Profile',   '/customer/profile.php'],
                    ] as [$lbl, $href]): ?>
                    <a href="<?= $href ?>" style="display:block; font-size:13px; color:rgba(255,255,255,0.5); margin-bottom:8px; text-decoration:none; transition:color 0.15s;"
                       onmouseover="this.style.color='#fff';" onmouseout="this.style.color='rgba(255,255,255,0.5)';">
                        <?= $lbl ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Contact -->
                <div class="col-lg-4">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:14px;">Contact</div>
                    <?php foreach([
                        ['bi-geo-alt-fill',    'Makati City, Metro Manila, Philippines'],
                        ['bi-telephone-fill',  '+63 2 8888 7777'],
                        ['bi-envelope-fill',   'support@reddoorz.ph'],
                        ['bi-clock-fill',      'Mon – Sun: 7:00 AM – 10:00 PM'],
                    ] as [$ic, $txt]): ?>
                    <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:10px;">
                        <i class="bi <?= $ic ?>" style="color:var(--rd-red); font-size:13px; margin-top:2px; flex-shrink:0;"></i>
                        <span style="font-size:13px; color:rgba(255,255,255,0.5); line-height:1.5;"><?= $txt ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div><!-- /row -->
        </div><!-- /container -->
    </div>

    <!-- Bottom bar -->
    <div style="background:#080606; padding: 14px 0;">
        <div class="container">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <p style="font-size:12px; color:rgba(255,255,255,0.28); margin:0;">
                    &copy; <?= date('Y') ?> RedDoorz Philippines. All rights reserved.
                </p>
                <div style="display:flex; gap:18px;">
                    <?php foreach(['Privacy Policy','Terms of Service','Cookie Policy'] as $lnk): ?>
                    <a href="#" style="font-size:12px; color:rgba(255,255,255,0.28); text-decoration:none; transition:color 0.15s;"
                       onmouseover="this.style.color='rgba(255,255,255,0.6)';" onmouseout="this.style.color='rgba(255,255,255,0.28)';">
                        <?= $lnk ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ===== /FOOTER ===== -->

</body>
</html>
