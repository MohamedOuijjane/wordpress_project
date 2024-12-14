<!-- variation sidebar -->
<div class="thd-main-sidebar">
    <div class="swatch-var-widget-wrap">
        <!-- widget -->

        <div class="swatch-var-widget">
            <h3 class="swatch-video-title"> <?php _e( 'Getting Started', 'wcvs' ) ?></h3>
            <div class="swat-video-frame">
                <iframe src="https://www.youtube.com/embed/1qGusf9IfFY" title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>
        <!-- widget -->

        <!-- widget -->
        <?php
        ob_start();

        $plugin_slug = 'variation-swatches-for-woocommerce';

        if ( ! $all_plugins = get_transient( 'woosuite-all-plugins' ) ) {
            $request = wp_remote_request( 'https://dw.woosuite.com/wp-json/woosuite-server/v1/plugins/' );

            if ( ! is_wp_error( $request ) ) {
                $all_plugins = json_decode( wp_remote_retrieve_body( $request ) );
                set_transient( 'woosuite-all-plugins', $all_plugins, DAY_IN_SECONDS );
            }
        }
        
        if ( $all_plugins ) {
            $current_plugin = array_filter( $all_plugins, function( $item ) use ( $plugin_slug ) {
                if ( $item->slug == $plugin_slug ) {
                    return $item;
                }
            } );

            if ( $current_plugin ) {
                sort( $current_plugin );
                $current_plugin = $current_plugin[0];
                $recommended_plugins = maybe_unserialize( maybe_unserialize( $current_plugin->recommended_plugins ) );

                if ( $recommended_plugins ) {
                    // add clicks data to array
                    foreach( $all_plugins as $plugin ) {
                        foreach ( $recommended_plugins as $key => $value ) {
                            if ( isset( $value['plugin'] ) && $value['plugin'] == $plugin->slug ) {
                                $recommended_plugins[$key]['clicks'] = $plugin->clicks;
                            }
                        }
                    }
                    
                    $keys = array_column( $recommended_plugins, 'clicks' );
                    if ( $keys) {
                        array_multisort( $keys, SORT_DESC, $recommended_plugins );

                        foreach( $recommended_plugins as $recommended_plugin ) {
                            if ( ! isset( $recommended_plugin['plugin']) ) {
                                continue;
                            }
    
                            $recommended_plugin_slug = $recommended_plugin['plugin'];
                            $plugin_data =  array_filter( $all_plugins, function( $item ) use ( $recommended_plugin_slug ) {
                                if ( $item->slug == $recommended_plugin_slug ) {
                                    return $item;
                                }
                            } );
    
                            if ( $plugin_data ) {
                                sort( $plugin_data );
                                $plugin_data = $plugin_data[0];
                                $title       = ! empty( $recommended_plugin['plugin']['title'] ) ? $recommended_plugin['plugin']['title'] : $plugin_data->name;
                                $description = ! empty( $recommended_plugin['plugin']['description'] ) ? $recommended_plugin['plugin']['description'] : $plugin_data->short_description;
                                $link        = ! empty( $recommended_plugin['plugin']['link'] ) ? $recommended_plugin['plugin']['link'] : $plugin_data->homepage;
                                ?>
                                <div class="swatch-var-addon">
                                    <a href="<?php echo $link; ?>" class="swatch-addon-link"><h2><?php echo $title; ?></h2></a>
                                    <p><?php echo $description; ?></p>
                                    <a href="<?php echo $link; ?>" class="swatch-submit-link update-clicks"><?php _e('Get It now','woosuite-core')?></a>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        }

        $html = ob_get_clean();

        if ( ! empty( $html ) ) {
            ?>
            <div class="swatch-var-widget">
                <h2 class="swatch-video-title"> <?php _e('Works well with..','woosuite-core'); ?></h2>
                <div class="swatch-var-addon-wrap">
                    <?php echo $html; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<!-- variation sidebar -->