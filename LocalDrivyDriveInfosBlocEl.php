<?php

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class LocalDrivyDriveInfosBlocEl extends \Elementor\Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve oEmbed widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'LocalDrivyDriveInfo';
    }

    /**
     * Get widget title.
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('LocalDrivy - Informations Drive');
    }

    /**
     * Get widget icon.
     *
     * Retrieve oEmbed widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-info-circle';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['localdrivy'];
    }

    /**
     * Register oEmbed widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Options', 'plugin-name'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'localdrivy'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Entrez un titre', 'localdrivy'),
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => __('Couleur du titre', 'localdrivy'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => __('Couleur du contenu', 'localdrivy'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render oEmbed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $title = $this->get_settings_for_display('title');
        $title_color = $this->get_settings_for_display('title_color');
        $content_color = $this->get_settings_for_display('content_color');
        $localDrivyApiClient = new LocalDrivyApiClient();
        $html = $localDrivyApiClient->getDriveInfos();
        $style="";
        if ($title_color!=null) {
            $style .="color:".$title_color;
        }
        echo '<h4  class="title" style="'.$style.'">'.$title.'</h4><div class="content" style="color:'.$content_color.'">'.$html.'</div>';
    }
    protected function _content_template()
    {
        $localDrivyApiClient = new LocalDrivyApiClient();
        $html = $localDrivyApiClient->getDriveInfos(); ?>
		<h4 class="title" style="color: {{ settings.title_color }}">{{{ settings.title }}} </h4><div class="content" style="color: {{ settings.content_color }}"><?php echo $html ; ?></div>
		<?php
    }
}
