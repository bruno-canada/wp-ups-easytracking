<?php

class UpsEasyTracking_Admin
{

    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;

        add_action('admin_init', array('UpsEasyTracking_Admin', 'admin_init'));

        add_action('admin_menu', array('UpsEasyTracking_Admin', 'admin_menu'), 5);

        //add Settings shortcut link to plugin installed page
        add_filter('plugin_action_links_' . WPUPS_PLUGIN_MAINFILE, array('UpsEasyTracking_Admin', 'admin_setting_link'));
    }

    public static function admin_init()
    {
        self::registerFields();
    }

    public static function admin_menu()
    {

        add_options_page(__('UPS Easy Tracking', 'wp-ups-easytracking'), __('UPS Easy Tracking', 'wp-ups-easytracking'), 'manage_options', 'wp-ups-easytracking', array('UpsEasyTracking_Admin', 'display_page'));
    }

    public static function admin_setting_link($links)
    {
        $newlink = "<a href='options-general.php?page=wp-ups-easytracking'>Settings</a>";
        array_push($links, $newlink);
        return $links;
    }

    public static function display_page()
    {
        include(WPUPS_PLUGIN_DIR . 'views/admin.php');
    }

    public static function registerFields()
    {
        $section = [

            'id' => 'wp-ups-easytracking',
            'title' => 'Settings',
            'callback' => '',
            'page' => 'wp-ups-easytracking'

        ];
        self::setSection($section);


        $settings = [
            [
                'option_group' => 'wp-ups-easytracking_options_group',
                'option_name' => '_wpups_accesskey',
                'callback' => array('UpsEasyTracking_Admin', 'buildOptionsSetting')
            ],
            [
                'option_group' => 'wp-ups-easytracking_options_group',
                'option_name' => '_wpups_username',
                'callback' => array('UpsEasyTracking_Admin', 'buildOptionsSetting')
            ],
            [
                'option_group' => 'wp-ups-easytracking_options_group',
                'option_name' => '_wpups_pass',
                'callback' => array('UpsEasyTracking_Admin', 'buildOptionsSetting')
            ],
            [
                'option_group' => 'wp-ups-easytracking_options_group',
                'option_name' => '_wpups_mode',
                'callback' => array('UpsEasyTracking_Admin', 'buildOptionsSetting')
            ]
        ];
        self::setSetting($settings);

        $fields = [
            [
                'id' => '_wpups_accesskey',
                'title' => 'Access Key',
                'callback' => array('UpsEasyTracking_Admin', 'buildAccessKeyField'),
                'page' => 'wp-ups-easytracking',
                'section' => 'wp-ups-easytracking',
                'args' => [
                    'label_for' => 'accesskey',
                    'class' => 'wpups-accesskey'
                ]
            ],
            [

                'id' => '_wpups_username',
                'title' => 'Username',
                'callback' => array('UpsEasyTracking_Admin', 'buildUsernameField'),
                'page' => 'wp-ups-easytracking',
                'section' => 'wp-ups-easytracking',
                'args' => [
                    'label_for' => 'username',
                    'class' => 'wpups-username'
                ]
            ],
            [
                'id' => '_wpups_pass',
                'title' => 'Password',
                'callback' => array('UpsEasyTracking_Admin', 'buildPasswordField'),
                'page' => 'wp-ups-easytracking',
                'section' => 'wp-ups-easytracking',
                'args' => [
                    'label_for' => 'pass',
                    'class' => 'wpups-pass'
                ]
            ],
            [
                'id' => '_wpups_mode',
                'title' => 'Mode',
                'callback' => array('UpsEasyTracking_Admin', 'buildModeField'),
                'page' => 'wp-ups-easytracking',
                'section' => 'wp-ups-easytracking',
                'args' => [
                    'label_for' => 'mode',
                    'class' => 'wpups-mode'
                ]
            ]
        ];
        self::setField($fields);
    }

    public static function buildOptionsSetting($input)
    {
        return $input;
    }

    public static function buildAccessKeyField()
    {
        $value = esc_attr(get_option('_wpups_accesskey'));
        echo "<input type='text' class='' name='_wpups_accesskey' value='" . $value . "' />";
    }
    public static function buildUsernameField()
    {
        $value = esc_attr(get_option('_wpups_username'));
        echo "<input type='text' class='' name='_wpups_username' value='" . $value . "' />";
    }
    public static function buildPasswordField()
    {
        $value = esc_attr(get_option('_wpups_pass'));
        echo "<input type='password' class='' name='_wpups_pass' value='" . $value . "' />";
    }
    public static function buildModeField()
    {
        $value = esc_attr(get_option('_wpups_mode'));
        echo "<select name='_wpups_mode'>
                <option value=''>----</option>
                <option " . (($value == "Test") ? "selected='selected'" : "") . ">Test</option>
                <option " . (($value == "Production") ? "selected='selected'" : "") . ">Production</option>
             </select>";
    }

    public static function setSection(array $section)
    {
        $secion_callback = !empty($section['callback']) ? $section['callback'] : '';
        add_settings_section($section['id'], $section['title'], $secion_callback, $section['page']);
    }

    public static function setSetting(array $settings)
    {
        foreach ($settings as $setting) {
            $setting_callback = !empty($setting['callback']) ? $setting['callback'] : '';
            register_setting($setting['option_group'], $setting['option_name'], $setting_callback);
        }
    }

    public static function setField(array $fields)
    {
        foreach ($fields as $field) {
            $field_callback = !empty($field['callback']) ? $field['callback'] : '';
            $field_args = !empty($field['args']) ? $field['args'] : '';
            add_settings_field($field['id'], $field['title'], $field['callback'], $field['page'], $field['section'], $field_args);
        }
    }
}
