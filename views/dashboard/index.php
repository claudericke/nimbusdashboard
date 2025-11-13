<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            
            <?php if(Session::has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo h(Session::get('success')); Session::remove('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(Session::has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo h(Session::get('error')); Session::remove('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="bento-grid">
                <div class="card wide p-4" style="background-image:url('https://dashboard.driftnimbus.com/assets/images/main.jpg');background-size:cover;background-position:center">
                    <div class="card-body">
                        <h1 class="text-white fw-bold mb-2">Welcome, <br><?php echo h($profileName); ?></h1>
                        <p class="text-white mb-1">Server Status: <span style="color:<?php echo $serverStatus?'#28a745':'#dc3545'; ?>;font-weight:bold"><?php echo $serverStatus?'Online':'Offline'; ?></span></p>
                        <p class="text-white mb-0">Active Package: <?php echo h($packageName??'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center" style="background-image:url('assets/images/weather/cloudy.jpg');background-size:cover;background-position:center">
                    <div class="card-body w-100">
                        <h5 class="card-title text-highlight text-white">Weather</h5>
                        <?php if($weather): ?>
                        <img src="assets/images/weather/<?php echo h($weatherIcon); ?>" alt="Weather" style="width:80px;height:80px">
                        <h4 class="mb-0 mt-2 text-white"><?php echo h($weather['temperature']??'N/A'); ?>°C</h4>
                        <?php else: ?>
                        <p class="text-white">Weather unavailable</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center" style="background-image:url('assets/images/bg-themes/clock.jpg');background-size:cover;background-position:center">
                    <div class="card-body w-100">
                        <h5 class="card-title text-highlight text-white">Current Time</h5>
                        <div class="time-widget text-white" id="time-widget"></div>
                        <p class="text-white">Harare, Zimbabwe</p>
                    </div>
                </div>
                
                <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center card-bg2">
                    <div class="card-body w-100">
                        <h5 class="card-title text-highlight text-white">Disk Usage</h5>
                        <i class="fas fa-hdd text-white" style="font-size:3rem"></i>
                        <?php if($diskUsage): ?>
                        <h4 class="mb-0 mt-2 text-white"><?php echo h($diskUsage['megabytes_used']??0/1024); ?> GB</h4>
                        <p class="text-white">Used Space</p>
                        <?php else: ?>
                        <h4 class="mb-0 mt-2 text-white">N/A</h4>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center card-bg2">
                    <div class="card-body w-100">
                        <h5 class="card-title text-highlight text-white" style="color:var(--ssl-green)!important">SSL Protected!</h5>
                        <i class="fas fa-lock text-white" style="font-size:3rem"></i>
                        <h4 class="mb-0 mt-2 text-white">Secure</h4>
                        <p class="text-white">Your connection is safe</p>
                        <a href="/ssl" class="btn btn-primary mt-3">View Certificates</a>
                    </div>
                </div>
                
                <div class="card wide p-4 d-flex flex-column justify-content-center quote-card" style="<?php echo !empty($quote['image_url'])?'background-image:url('.h($quote['image_url']).')':'background-image:url(https://dashboard.driftnimbus.com/assets/images/quote.jpg)'; ?>">
                    <div class="quote-overlay"></div>
                    <div class="card-body quote-content">
                        <h5 class="card-title text-highlight text-white">Quote of the Day</h5>
                        <?php if($quote): ?>
                        <p class="text-white fst-italic" style="font-size:1.5rem;margin-top:30px">"<?php echo h($quote['quote_text']); ?>"</p>
                        <footer class="blockquote-footer text-white mt-2"><cite><?php echo h($quote['author']); ?></cite></footer>
                        <?php else: ?>
                        <p class="text-white">Quote unavailable.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
