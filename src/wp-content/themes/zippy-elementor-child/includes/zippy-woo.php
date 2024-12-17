<?php

function enqueue_wc_cart_fragments() { wp_enqueue_script( 'wc-cart-fragments' ); } add_action( 'wp_enqueue_scripts', 'enqueue_wc_cart_fragments' );