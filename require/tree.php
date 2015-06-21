<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 19/02/14
 * Time: 9:02 PM
 */

class ImageTree
{
    // Configuration variables
    private $text_size = 12;
    private $padding = 40;
    private $edge_padding = 30;
    private $arrow_radius = 3;

    private $font_filename;
    private $text_height = 0;

    // Image variables
    private $img = NULL;
    private $color_white;
    private $color_red;
    private $color_black;

    // Boundary calculation
    private $max_x = 0;
    private $max_y = 0;
    private $min_x = 0;
    private $min_y = 0;

    // Data
    private $names_by_id;
    private $children_by_id;
    private $leaf_counts = array();
    private $total_leaves = 0;

    // Constructor
    public function __construct()
    {
        $this->font_filename = dirname(__FILE__) . "/../arial.ttf";
    }
    public function __destruct()
    {
        if($this->img != NULL)
            imagedestroy($this->img);
    }
    public function load_data($names_by_id, $children_by_id)
    {
        $this->names_by_id = $names_by_id;
        $this->children_by_id = $children_by_id;

        $this->total_leaves = $this->count_leaves(NULL);
    }
    public function init()
    {
        $bounds = $this->text_bounds("X", 0);
        $this->text_height = $bounds[7] - $bounds[1];
        $this->calc_tree_bounds($this->children_by_id[NULL], $this->total_leaves, $this->padding);
        $width = intval($this->max_x-$this->min_x)+$this->edge_padding*2;
        $height = intval($this->max_y-$this->min_y)+$this->edge_padding*2;

        $this->img = @imagecreate($width, $height);

        $this->color_black = imagecolorallocate($this->img, 0, 0, 0);
        $this->color_white = imagecolorallocate($this->img, 225, 225, 225);
        $this->color_red = imagecolorallocate($this->img, 225, 0, 0);
    }
    public function draw()
    {
        $x = -$this->min_x + $this->edge_padding;
        $y = -$this->min_y + $this->edge_padding;
        $positions = $this->draw_tree( $this->children_by_id[NULL], $this->total_leaves, $this->padding);
        foreach($positions as $p)
        {
            $this->draw_arrow( $x, $y, $p["x"], $p["y"] );
        }
        $bounds = $this->text_bounds("OZ", 0);
        $ozw = $bounds[2]-$bounds[0];
        $ozh = $bounds[7]-$bounds[1];
        $this->draw_text("OZ", $x-$ozw/2, $y-$ozh/2, 0);
    }

    public function output()
    {
        imagepng($this->img);
    }

    // Helper functions
    private function draw_text( $text, $x, $y, $angle )
    {
        imagettftext($this->img, $this->text_size, rad2deg($angle), $x, $y, $this->color_white, $this->font_filename, $text);
    }
    private function draw_arrow( $x1, $y1, $x2, $y2 )
    {
        imageline( $this->img, $x1, $y1, $x2, $y2, $this->color_red );

        $px = $y2-$y1;
        $py = $x2-$x1;
        $py *= -1;

        $len = sqrt( ($px)*($px) + ($py)*($py) );
        $px /= $len;
        $px *= $this->arrow_radius;
        $py /= $len;
        $py *= $this->arrow_radius;

        $dx = $x2-$x1;
        $dy = $y2-$y1;
        $dx /= $len;
        $dy /= $len;
        $dx *= ($len-$this->arrow_radius);
        $dy *= ($len-$this->arrow_radius);

        $dx += $px;
        $dy += $py;
        imageline( $this->img, $x2, $y2, $x1+$dx, $y1+$dy, $this->color_red );

        $dx -= 2*$px;
        $dy -= 2*$py;
        imageline( $this->img, $x2, $y2, $x1+$dx, $y1+$dy, $this->color_red );
    }
    private function text_bounds( $text, $angle, $x=0, $y=0 )
    {
        $bounds = imagettfbbox( $this->text_size, rad2deg($angle), $this->font_filename, $text );
        for($i = 0; $i < 8; $i += 2)
        {
            $bounds[$i] += $x;
            $bounds[$i+1] += $y;
        }
        return $bounds;
    }
    private function text_width( $text )
    {
        $bounds = $this->text_bounds($text, 0);
        return $bounds[2]-$bounds[0];
    }
    function extend_bounds( $new_bound )
    {
        for( $i = 0; $i < 8; $i += 2 )
        {
            $x = $new_bound[$i];
            $y = $new_bound[$i+1];
            if( $x > $this->max_x ) $this->max_x = $x;
            if( $y > $this->max_y ) $this->max_y = $y;
            if( $x < $this->min_x ) $this->min_x = $x;
            if( $y < $this->min_y ) $this->min_y = $y;
        }
    }

    private function count_leaves( $id )
    {
        if(!isset($this->children_by_id[$id])) return 1;
        $result = 0;
        foreach($this->children_by_id[$id] as $child_id)
        {
            $delta = $this->count_leaves($child_id);
            $result += $delta;
            $this->leaf_counts[$child_id] = $delta;
        }
        return $result;
    }

    private function calc_tree_bounds($children, $leaf_count, $radius, $ang_start=0, $ang_range=-1, $level=1)
    {
        if($children == NULL) return;
        if($ang_range == -1) $ang_range = 2*pi();
        $x = 0;
        $y = 0;
        $max_width = 0;

        $angle_per_leaf = $ang_range/$leaf_count;
        $angle = $ang_start;

        foreach($children as $child_id)
        {
            $old_angle = $angle;
            $this->do_single_name($radius, $child_id, $angle_per_leaf, $angle, $max_width, $x, $y, false);
            $new_angle = $angle;

            if(isset($this->children_by_id[$child_id]))
            {
                $this->calc_tree_bounds($this->children_by_id[$child_id], $this->leaf_counts[$child_id],
                    $radius+$max_width+$this->padding*$level, $old_angle, $new_angle-$old_angle, $level+1);
            }
        }
    }
    private function draw_tree($children, $leaf_count, $radius, $ang_start=0, $ang_range=-1, $level=1)
    {
        if($children == NULL) return NULL;
        if($ang_range == -1) $ang_range = 2*pi();
        $x = -$this->min_x+$this->edge_padding;
        $y = -$this->min_y+$this->edge_padding;
        $max_width = 0;

        $angle_per_leaf = $ang_range/$leaf_count;
        $angle = $ang_start;

        $positions1 = array();
        $positions2 = array();
        $angles = array();

        foreach($children as $child_id)
        {
            array_push($angles, $angle);
            $this->do_single_name($radius, $child_id, $angle_per_leaf, $angle, $max_width, $x, $y, true, $positions1, $positions2);
        }
        array_push($angles, $ang_start+$ang_range);

        $i = 0;
        foreach($children as $child_id)
        {
            if(isset($this->children_by_id[$child_id]))
            {
                $old_angle = $angles[$i];
                $new_angle = $angles[$i+1];
                $new_positions = $this->draw_tree($this->children_by_id[$child_id], $this->leaf_counts[$child_id],
                    $radius+$max_width+$this->padding*$level, $old_angle, $new_angle-$old_angle, $level+1);

                foreach($new_positions as $new_pos)
                {
                    $this->draw_arrow( $positions2[$i]["x"], $positions2[$i]["y"], $new_pos["x"], $new_pos["y"] );
                }
            }
            $i++;
        }
        return $positions1;
    }

    private function do_single_name($radius, $child_id, $angle_per_leaf,
                                    &$angle, &$max_width, $x, $y, $draw, &$positions1=NULL, &$positions2=NULL)
    {
        $name = $this->names_by_id[$child_id];
        $delta = $this->leaf_counts[$child_id] * $angle_per_leaf;
        $angle += $delta / 2;

        $shift_x = -sin($angle) * $this->text_height / 2;
        $shift_y = -cos($angle) * $this->text_height / 2;
        $dx = cos($angle) * $radius;
        $dy = -sin($angle) * $radius;
        $text_width = $this->text_width($name);
        if ($text_width > $max_width) $max_width = $text_width;

        if(!$draw)
        {
            $bounds = $this->text_bounds($name, $angle, $x + $dx + $shift_x, $y + $dy + $shift_y);
            $this->extend_bounds($bounds);
        }
        else
        {
            $this->draw_text($name, $x+$dx+$shift_x, $y+$dy+$shift_y, $angle);
        }
        $angle += $delta / 2;

        if($positions1 !== NULL)
            array_push($positions1, array("x" => $x+$dx, "y" => $y+$dy ));
        if($positions2 !== NULL)
            array_push($positions2, array(
                "x" => $x+$dx/$radius*($radius+$text_width),
                "y" => $y+$dy/$radius*($radius+$text_width)));
    }
}