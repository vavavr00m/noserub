<?php
/**
 * Helper to display xfn for editing
 */
class XfnHelper extends AppHelper {
    
    public function friendship($data) {
        $checked = $this->checked($data, array(1, 2, 3));
        return $this->radio('friendship', 0, $checked, '<em>' . __('no selection', true) . '</em>', true) .
               $this->radio('friendship', 3, $checked, __('contact', true)) .
               $this->radio('friendship', 2, $checked, __('acquaintance', true)) .
               $this->radio('friendship', 1, $checked, __('friend', true));
    }

    public function geographical($data) {
        $checked = $this->checked($data, array(8, 9));
        return $this->radio('geographical', 0, $checked, '<em>' . __('no selection', true) . '</em>', true) .
               $this->radio('geographical', 8, $checked, __('co-resident', true)) .
               $this->radio('geographical', 9, $checked, __('neighbor', true));
    }
    
    public function family($data) {
        $checked = $this->checked($data, array(10, 11, 12, 13, 14));
        return $this->radio('familiy', 0, $checked, '<em>' . __('no selection', true) . '</em>', true) .
               $this->radio('familiy', 10, $checked, __('child', true)) .
               $this->radio('familiy', 11, $checked, __('parent', true)) .
               $this->radio('familiy', 12, $checked, __('sibling', true)) .
               $this->radio('familiy', 13, $checked, __('spouse', true)) .
               $this->radio('familiy', 14, $checked, __('kin', true));
    }
    
    public function physical($data) {
        $checked = $this->checked($data, array(4, 5));
        return $this->radio('physical', 0, $checked, '<em>' . __('no selection', true) . '</em>', true) .
               $this->radio('physical', 4, $checked, __('met', true)) .
               $this->radio('physical', 5, $checked, __('want-to-meet', true));
    }
    
    private function radio($name, $value, $checked, $label, $hidejs = false) {
        if($checked == $value) {
            $checked = ' checked="checked" ';
            $checked_css = ' checked';
        } else {
            $checked = '';
            $checked_css = '';
        }

        $hidejs_css = $hidejs ? ' hidejs' : '';
        return '<input id="'.$name.$value.'" type="radio" name="data[xfn]['.$name.']" value="'.$value.'"'. $checked . '/><label for="'.$name.$value.'" class="contact_type radio'.$hidejs_css.$checked_css.'">'.$label.'</label>';
    }
    
    private function checked($data, $ids) {
        foreach($data as $item) {
            foreach($ids as $id) {
                if($item == $id) {
                    return $id;
                }
            }
        }
        
        return 0;
    }
}