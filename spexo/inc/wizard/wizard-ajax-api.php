<?php 

if ( ! function_exists( 'tmpcoder_theme_wizard_is_one_step_flow_enabled' ) ) {
    /**
     * Phase 1 contract: one-step wizard flow is the default path.
     *
     * @return bool
     */
    function tmpcoder_theme_wizard_is_one_step_flow_enabled() {
        return (bool) apply_filters( 'tmpcoder_theme_wizard_one_step_flow_enabled', true );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_target_plugin_file' ) ) {
    /**
     * Addon activation gate plugin file.
     *
     * @return string
     */
    function tmpcoder_theme_wizard_target_plugin_file() {
        return 'sastra-essential-addons-for-elementor/sastra-essential-addons-for-elementor.php';
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_setup_plugin_files' ) ) {
    /**
     * Canonical plugin basenames for the theme wizard one-step flow (install order).
     *
     * Order: Elementor → Redux Framework → Spexo Addon for Elementor.
     *
     * @return string[]
     */
    function tmpcoder_theme_wizard_setup_plugin_files() {
        return array(
            'elementor/elementor.php',
            'redux-framework/redux-framework.php',
            tmpcoder_theme_wizard_target_plugin_file(),
        );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_get_setup_plugin_jobs' ) ) {
    /**
     * Ordered install/activate jobs for the one-step wizard (WordPress.org / repo plugins).
     *
     * Uses canonical plugin basenames — not TGMPA's {@see _get_plugin_basename_from_slug()} output,
     * which is only a bare slug when the plugin is not yet installed.
     *
     * @return array<int, array{slug:string,file_path:string,name:string}>
     */
    function tmpcoder_theme_wizard_get_setup_plugin_jobs() {
        $file_labels = array(
            'elementor/elementor.php'                     => __( 'Elementor', 'spexo' ),
            'redux-framework/redux-framework.php'         => __( 'Redux Framework', 'spexo' ),
            tmpcoder_theme_wizard_target_plugin_file()   => __( 'Spexo Addons for Elementor', 'spexo' ),
        );

        $jobs = array();
        foreach ( tmpcoder_theme_wizard_setup_plugin_files() as $file_path ) {
            if ( ! is_string( $file_path ) || '' === $file_path ) {
                continue;
            }
            $slug = dirname( $file_path );
            if ( '' === $slug || '.' === $slug ) {
                continue;
            }
            $jobs[] = array(
                'slug'       => $slug,
                'file_path'  => $file_path,
                'name'       => isset( $file_labels[ $file_path ] ) ? $file_labels[ $file_path ] : $slug,
            );
        }

        return $jobs;
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_setup_redirect_url' ) ) {
    /**
     * Redirect target for successful one-step flow.
     *
     * @return string
     */
    function tmpcoder_theme_wizard_setup_redirect_url() {
        return admin_url( 'admin.php?page=tmpcoder-setup-wizard' );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_is_target_plugin_active' ) ) {
    /**
     * Check whether the required Spexo addon plugin is active.
     *
     * @return bool
     */
    function tmpcoder_theme_wizard_is_target_plugin_active() {
        return tmpcoder_theme_wizard_is_plugin_marked_active( tmpcoder_theme_wizard_target_plugin_file() );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_are_setup_plugins_active' ) ) {
    /**
     * Whether every plugin required by the one-step theme wizard is active.
     *
     * @return bool
     */
    function tmpcoder_theme_wizard_are_setup_plugins_active() {
        foreach ( tmpcoder_theme_wizard_setup_plugin_files() as $plugin_file ) {
            if ( ! tmpcoder_theme_wizard_is_plugin_marked_active( $plugin_file ) ) {
                return false;
            }
        }
        return true;
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_is_plugin_marked_active' ) ) {
    /**
     * Theme-check-safe active plugin lookup.
     *
     * @param string $plugin_file Plugin base file path.
     * @return bool
     */
    function tmpcoder_theme_wizard_is_plugin_marked_active( $plugin_file ) {
        $active_plugins = tmpcoder_theme_wizard_normalize_active_plugins_option();
        if ( in_array( $plugin_file, $active_plugins, true ) ) {
            return true;
        }

        if ( is_multisite() ) {
            $network_active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
            if ( isset( $network_active_plugins[ $plugin_file ] ) ) {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_normalize_active_plugins_option' ) ) {
    /**
     * Normalize malformed `active_plugins` option values before core activation reads them.
     *
     * @return string[]
     */
    function tmpcoder_theme_wizard_normalize_active_plugins_option() {
        $raw_active_plugins = get_option( 'active_plugins', array() );
        $normalized_plugins = array();

        if ( is_array( $raw_active_plugins ) ) {
            $normalized_plugins = $raw_active_plugins;
        } elseif ( is_string( $raw_active_plugins ) ) {
            $maybe_unserialized = maybe_unserialize( $raw_active_plugins );

            if ( is_array( $maybe_unserialized ) ) {
                $normalized_plugins = $maybe_unserialized;
            } else {
                $single_plugin = trim( $raw_active_plugins );

                if ( '' !== $single_plugin && false !== strpos( $single_plugin, '.php' ) ) {
                    $normalized_plugins = array( $single_plugin );
                }
            }
        }

        $normalized_plugins = array_values(
            array_filter(
                array_map(
                    static function ( $plugin_file ) {
                        return is_string( $plugin_file ) ? trim( $plugin_file ) : '';
                    },
                    $normalized_plugins
                ),
                static function ( $plugin_file ) {
                    return '' !== $plugin_file;
                }
            )
        );

        $current_plugins = is_array( $raw_active_plugins )
            ? array_values(
                array_filter(
                    array_map(
                        static function ( $plugin_file ) {
                            return is_string( $plugin_file ) ? trim( $plugin_file ) : '';
                        },
                        $raw_active_plugins
                    ),
                    static function ( $plugin_file ) {
                        return '' !== $plugin_file;
                    }
                )
            )
            : array();

        if ( ! is_array( $raw_active_plugins ) || $normalized_plugins !== $current_plugins ) {
            update_option( 'active_plugins', $normalized_plugins );
        }

        return $normalized_plugins;
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_activate_plugin_safely' ) ) {
    /**
     * Activate a plugin while recovering from malformed active plugin state.
     *
     * @param string $plugin_file Plugin base file path.
     * @return null|WP_Error
     */
    function tmpcoder_theme_wizard_activate_plugin_safely( $plugin_file ) {
        $plugin_file = plugin_basename( trim( $plugin_file ) );

        if ( '' === $plugin_file ) {
            return new WP_Error( 'invalid_plugin', __( 'Invalid plugin file.', 'spexo' ) );
        }

        $active_plugins = get_option( 'active_plugins', array() );

        if ( is_array( $active_plugins ) ) {
            try {
                return activate_plugin( $plugin_file );
            } catch ( Throwable $exception ) {
                return new WP_Error( 'plugin_activation_failed', $exception->getMessage() );
            }
        }

        if ( in_array( $plugin_file, tmpcoder_theme_wizard_normalize_active_plugins_option(), true ) ) {
            return null;
        }

        $buffer_level = ob_get_level();

        try {
            $valid = validate_plugin( $plugin_file );
            if ( is_wp_error( $valid ) ) {
                return $valid;
            }

            $requirements = validate_plugin_requirements( $plugin_file );
            if ( is_wp_error( $requirements ) ) {
                return $requirements;
            }

            ob_start();

            // Follow core activation steps after repairing malformed option state.
            plugin_sandbox_scrape( $plugin_file );
            do_action( 'activate_plugin', $plugin_file, false );
            do_action( "activate_{$plugin_file}", false );

            $current_plugins   = tmpcoder_theme_wizard_normalize_active_plugins_option();
            $current_plugins[] = $plugin_file;
            $current_plugins   = array_values( array_unique( $current_plugins ) );
            sort( $current_plugins );

            update_option( 'active_plugins', $current_plugins );
            do_action( 'activated_plugin', $plugin_file, false );

            if ( ob_get_length() > 0 ) {
                $output = ob_get_clean();
                return new WP_Error( 'unexpected_output', __( 'The plugin generated unexpected output.', 'spexo' ), $output );
            }

            ob_end_clean();

            return null;
        } catch ( Throwable $exception ) {
            while ( ob_get_level() > $buffer_level ) {
                ob_end_clean();
            }

            return new WP_Error( 'plugin_activation_failed', $exception->getMessage() );
        }
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_can_manage_setup' ) ) {
    /**
     * Centralized setup capability check.
     *
     * @return bool
     */
    function tmpcoder_theme_wizard_can_manage_setup() {
        return is_user_logged_in() && current_user_can( 'install_plugins' );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_send_auth_error' ) ) {
    /**
     * Standard auth/capability error response.
     *
     * @return void
     */
    function tmpcoder_theme_wizard_send_auth_error() {
        wp_send_json_error(
            array(
                'message' => __( 'You do not have permission to run theme setup.', 'spexo' ),
            )
        );
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_get_tgmpa_plugins' ) ) {
    /**
     * Get registered TGMPA plugins for the wizard.
     *
     * @return array
     */
    function tmpcoder_theme_wizard_get_tgmpa_plugins() {
        $tgmpa_class = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : null;

        if ( is_object( $tgmpa_class ) && ! empty( $tgmpa_class->plugins ) ) {
            return $tgmpa_class->plugins;
        }

        $tmpcoder_main_class = new Tmpcoder_Main_Class();
        $tmpcoder_main_class->tmpcoder_require_plugins();
        $tgmpa_class = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : null;

        return ( is_object( $tgmpa_class ) && ! empty( $tgmpa_class->plugins ) ) ? $tgmpa_class->plugins : array();
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_install_and_activate_plugin' ) ) {
    /**
     * Install/update and activate one plugin (repo) using canonical basename and folder slug.
     *
     * @param array $plugin Must include `file_path` (e.g. elementor/elementor.php). `slug` optional (defaults to directory of file_path).
     * @return true|WP_Error
     */
    function tmpcoder_theme_wizard_install_and_activate_plugin( $plugin ) {
        if ( empty( $plugin['file_path'] ) || ! is_string( $plugin['file_path'] ) ) {
            return new WP_Error( 'invalid_plugin_data', __( 'Invalid plugin data provided.', 'spexo' ) );
        }

        $plugin_file_path = $plugin['file_path'];
        $plugin_slug      = ! empty( $plugin['slug'] ) && is_string( $plugin['slug'] ) ? $plugin['slug'] : dirname( $plugin_file_path );

        if ( '' === $plugin_slug || '.' === $plugin_slug ) {
            return new WP_Error( 'invalid_plugin_data', __( 'Invalid plugin data provided.', 'spexo' ) );
        }

        $resolved_path = tmpcoder_plugin_basefile_path( $plugin_slug );
        $on_disk_path   = ! empty( $resolved_path )
            ? $resolved_path
            : ( tmpcoder_is_plugin_installed( $plugin_file_path ) ? $plugin_file_path : '' );

        if ( '' !== $on_disk_path ) {
            tmpcoder_update_plugin( $on_disk_path );
        } else {
            $installed = tmpcoder_install_plugin( $plugin_slug );
            if ( is_wp_error( $installed ) ) {
                return $installed;
            }
            if ( false === $installed || null === $installed ) {
                return new WP_Error( 'plugin_install_failed', __( 'Plugin installation failed.', 'spexo' ) );
            }
        }

        $resolved_path = tmpcoder_plugin_basefile_path( $plugin_slug );
        if ( empty( $resolved_path ) ) {
            return new WP_Error( 'plugin_file_not_found', __( 'Installed plugin file not found.', 'spexo' ) );
        }

        if ( ! tmpcoder_theme_wizard_is_plugin_marked_active( $resolved_path ) ) {
            $activate = tmpcoder_theme_wizard_activate_plugin_safely( $resolved_path );
            if ( is_wp_error( $activate ) ) {
                return $activate;
            }
        }

        return true;
    }
}

if ( ! function_exists( 'tmpcoder_theme_wizard_mark_completed' ) ) {
    /**
     * Persist final wizard completion state for one-step flow.
     *
     * @return void
     */
    function tmpcoder_theme_wizard_mark_completed() {
        delete_option( TMPCODER_THEME_SLUG . '_wizard_step' );
        update_option( TMPCODER_THEME_SLUG . '_wizard_done', 1 );
        update_option( 'sastra_addons_wizard_page', 1 );
        update_option( 'spexo_addons_wizard_page', 1 );
    }
}

add_action( 'wp_ajax_tmpcoder_theme_wizard_one_step_setup', 'tmpcoder_theme_wizard_one_step_setup' );
function tmpcoder_theme_wizard_one_step_setup() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tmpcoder_theme_wizard_one_step_setup' ) ) {
        wp_send_json_error(
            array(
                'message' => __( 'Security check failed. Please refresh and try again.', 'spexo' ),
            )
        );
    }

    if ( ! tmpcoder_theme_wizard_can_manage_setup() ) {
        tmpcoder_theme_wizard_send_auth_error();
    }

    // Fast-path: if required plugins are already active, complete and redirect immediately.
    if ( tmpcoder_theme_wizard_are_setup_plugins_active() ) {
        tmpcoder_theme_wizard_mark_completed();
        wp_send_json_success(
            array(
                'message'      => __( 'Spexo setup is already ready. Redirecting...', 'spexo' ),
                'redirect_url' => esc_url_raw( tmpcoder_theme_wizard_setup_redirect_url() ),
                'already_active' => true,
            )
        );
    }

    /**
     * One-step wizard: Elementor → Redux → Spexo Addon (canonical list; not TGMPA file_path,
     * which is a bare slug until the plugin exists on disk).
     */
    $plugins = tmpcoder_theme_wizard_get_setup_plugin_jobs();
    if ( empty( $plugins ) ) {
        wp_send_json_error(
            array(
                'message' => __( 'Required plugins are not configured for setup.', 'spexo' ),
            )
        );
    }

    $failed_plugins = array();
    foreach ( $plugins as $plugin ) {
        $result = tmpcoder_theme_wizard_install_and_activate_plugin( $plugin );
        if ( is_wp_error( $result ) ) {
            $name = ! empty( $plugin['name'] ) ? $plugin['name'] : ( ! empty( $plugin['slug'] ) ? $plugin['slug'] : $plugin['file_path'] );
            $failed_plugins[] = $name . ': ' . $result->get_error_message();
        }
    }

    if ( ! empty( $failed_plugins ) ) {
        wp_send_json_error(
            array(
                'message' => sprintf(
                    /* translators: %s list of plugin errors. */
                    __( 'Setup failed for some plugins: %s', 'spexo' ),
                    implode( ', ', $failed_plugins )
                ),
            )
        );
    }

    if ( ! tmpcoder_theme_wizard_are_setup_plugins_active() ) {
        wp_send_json_error(
            array(
                'message' => __( 'Some required plugins are not active yet. Please try again.', 'spexo' ),
            )
        );
    }

    tmpcoder_theme_wizard_mark_completed();

    wp_send_json_success(
        array(
            'message'      => __( 'Recommended plugins installed and activated successfully.', 'spexo' ),
            'redirect_url' => esc_url_raw( tmpcoder_theme_wizard_setup_redirect_url() ),
        )
    );
}