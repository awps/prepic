<?php

namespace PrePic;

class Admin
{

    protected $current_grid_columns = false;
    protected $grid_is_open = false;
    protected $admin_page_slug = 'prepic';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        if (!empty($_GET['page']) && $_GET['page'] === $this->admin_page_slug) {
            wp_enqueue_style('zgrid', PREPIC_URI . 'node_modules/zgrid/zgrid.css');
            echo '<style>img{max-width: 100%; height: auto;}</style>';
        }
    }

    public function addPage()
    {
        add_menu_page(
            'PrePic',
            'PrePic',
            'manage_options',
            $this->admin_page_slug,
            [$this, 'page']
        );
    }

    public function page()
    {
        $image_url = '//awp.science/prepic/wp-content/uploads/sites/7/2017/12/wallpaper.jpg';
        self::dd($image_url);

        $this->zgOpen(false, 'Vertical crop positions');

        $image = prepic($image_url);

        var_dump($image);

        $this->cell(
            $image
                ->width(345)
                ->height(120)
                ->cropTop()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(345)
                ->height(120)
                ->crop()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(345)
                ->height(120)
                ->cropBottom()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $this->zgOpen(false, 'Horizontal crop positions');

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(170)
                ->height(320)
                ->cropLeft()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(170)
                ->height(320)
                ->crop()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(170)
                ->height(320)
                ->cropRight()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $this->zgOpen(false, 'Auto sizes');

        $image = prepic($image_url);
        $this->cell(
            $image
                ->height(200)
                ->crop()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(200)
                ->crop()
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

        $image = prepic($image_url);
        $this->cell(
            $image
                ->width(150)
                ->save()
                ->getImg()
            ,
            $image->getCropPositions()
        );

//		self::dd( wp_get_attachment_metadata(6) );
//		self::dd( $image->getUploadPath() );
//		self::dd( $image->getUploadUrl() );
//
//		self::dd( $image->getFileSystemRoutes() );

        $this->zgClose();
    }

    public static function dd($val)
    {
        echo '<pre>';
        print_r(esc_html(var_export($val)) . PHP_EOL);
        echo '</pre>';
    }

    public function zgOpen($cells = false, $title = false)
    {
        $this->zgClose();

        if (!empty($title)) {
            echo '<h3>' . $title . '</h3>';
        }

        $cells = !empty($cells) && $cells > 0 ? ' zg--' . $cells . 'columns' : false;
        echo '<div class="zg' . $cells . '">';

        $this->current_grid_columns = $cells;
        $this->grid_is_open = true;
    }

    public function zgClose()
    {
        if ($this->grid_is_open) {
            echo '</div>';
        }

        $this->grid_is_open = false;
    }

    public function cell()
    {
        $args = func_get_args();
        $cell_class = !empty($this->current_grid_columns) ? 'cell' : 'cell--auto';

        echo '<div class="' . $cell_class . '">';
        foreach ($args as $arg) {
            if (is_array($arg)) {
                self::dd($arg);
            } else {
                echo $arg;
            }
        }
        echo '</div>';
    }

}
