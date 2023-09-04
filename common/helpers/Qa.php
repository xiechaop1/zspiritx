<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Qa
{
    public static function formatQa($qa)
    {
        $qa['attachment'] = Attachment::completeUrl($qa['attachment'], true);
    }

    public static function formatSelect($qa) {
        $str = $qa['selected_json'];
        $str = str_replace("[div]", '<div>', $str);
        $str = str_replace("[/div]", '</div>', $str);
        $answers = ['A', 'B', 'C', 'D'];
        foreach ($answers as $an) {
            $optstr = '<div class="form-check form-check-inline m-t-5">';
            $optstr .= '<input class="form-check-input"  type=radio name="answer" value="' . $an . '" id="answer-' . $an . '">';
            $labelstr = '<label class="form-check-label fs-30 text-66" for="answer-' . $an . '">';
            //. $an .'</label></div>';
            $findstr = '[opt ' . $an . ']';
            $str = str_replace($findstr, $optstr, $str);

            $findstr = '[label ' . $an . ']';
            $str = str_replace($findstr, $labelstr, $str);
        }
        $str = str_replace('[/label]', '</label>', $str);
        $str = str_replace('[/opt]', '</div>', $str);
        return $str;
    }


}