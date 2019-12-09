<h1><?php esc_html_e('UPS Easy Tracking - Settings', 'wp-ups-easytracking'); ?></h1>


<p>Enter your UPS credentials.</p>

<p>To obtain your web service credentials, please, access: <a href='https://www.ups.com/upsdeveloperkit' target='_blank'>https://www.ups.com/upsdeveloperkit</a></p>

<h2>How to Use</h2>

<p>Just add the shortcode below to any page or post:<br /><strong>[wpups]</strong></p>

<div>
    <?php //settings_errors();?>

    <form method="post" action="options.php">
        <?php
            settings_fields('wp-ups-easytracking_options_group');
            do_settings_sections('wp-ups-easytracking');
            submit_button();
        ?>
    </form>
</div>