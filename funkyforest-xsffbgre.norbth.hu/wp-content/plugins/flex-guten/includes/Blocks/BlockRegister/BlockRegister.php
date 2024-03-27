<?php
namespace Dwp\Blocks\BlockRegister;
use Dwp;

class BlockRegister
{
    public $asset;    

    function __construct(){
        add_action( 'init', [ $this, 'flexguten_blocks_init' ] );
        $this->asset = new Dwp\Assets();
    }

    /**
     * Blocks Register
    */
    public function flexguten_register_block( $name, $options = array() ) {
        register_block_type( FLEXGUTEN_PATH . '/build/blocks/' . $name, $options );
    }

    /**
     * Blocks Initialization
    */
    public function flexguten_blocks_init() {        
        
        /**
         * Register Pinterest Block
        */
        $this->flexguten_register_block( 'pinterest-style-one',[
            'render_callback' => [$this,'pinterest_style_one_render_callback']
        ]  );       
        
        /**
         * Register Amazon Review Block
        */
        $this->flexguten_register_block( 'amazon-review-one',[
            'render_callback' => [$this,'amazon_review_one_render_callback']
        ]  );      
        
        /**
         * Register Post Grid Block
        */
        $this->flexguten_register_block( 'post-grid-one',[
            'render_callback' => [$this,'post_grid_block_render_callback']
        ]  );
        
    }

    /**
     * Pinterest Style One Callback
    */
    public function pinterest_style_one_render_callback($attr, $output){

        $PinterestBlock = new Dwp\Blocks\BlockStyle\Pinterest\PinterestStyleOne();
        $id = $attr['id'];

        /**
         * Pinterest Style One Editor Style
        */
        $this->asset->flexguten_inline_style(
            $id,
            $PinterestBlock->flexguten_frontend_styles($attr, $id )
        );
        
        /**
         * Pinterest Style One Frontend
        */
        return $PinterestBlock->flexguten_frontend_render( $attr, $id );

    }

    /**
     * Amazon Review One Callback
    */
    public function amazon_review_one_render_callback($attr, $output){

        $AmazonReviewOne = new Dwp\Blocks\BlockStyle\Amazon\AmazonReviewOne();
        $id = $attr['id'];

        /**
         * Amazon Style One Editor Style
        */
        $this->asset->flexguten_inline_style(
            $id,
            $AmazonReviewOne->flexguten_frontend_styles($attr, $id )
        );
        
        /**
         * Amazon Style One Frontend
        */
        //return $AmazonReviewOne->flexguten_frontend_render( $attr, $id );

        return $output;

    }

    /**
     * Post Grid One Render Callback
    */
    public function post_grid_block_render_callback($attr, $output){

        $PostGridOne = new Dwp\Blocks\BlockStyle\PostGridOne\PostGridOne();
        $id = $attr['id'];

        /**
         * Post Grid One Editor Style
        */
        $this->asset->flexguten_inline_style(            
            $id,
            $PostGridOne->flexguten_frontend_styles($attr, $id )
        );
        
        /**
         * Post Grid One Frontend
        */
        return $PostGridOne->flexguten_frontend_render( $attr, $id );

        return $output;

    }

}
