<?php
/**
 * Helper to display xfn for editing
 */
class XfnHelper extends AppHelper {
    
    public function friendship($data) {
        $checked = $this->checked($data, array(1, 2, 3));
        return $this->radio('friendship', 0, $checked, '<em>no selection</em>') .
               $this->radio('friendship', 3, $checked, 'contact') .
               $this->radio('friendship', 2, $checked, 'acquaintance') .
               $this->radio('friendship', 1, $checked, 'friend');
    }

    public function geographical($data) {
        $checked = $this->checked($data, array(8, 9));
        return $this->radio('geographical', 0, $checked, '<em>no selection</em>') .
               $this->radio('geographical', 8, $checked, 'co-resident') .
               $this->radio('geographical', 9, $checked, 'neighbor');
    }
    
    public function family($data) {
        $checked = $this->checked($data, array(10, 11, 12, 13, 14));
        return $this->radio('familiy', 0, $checked, '<em>no selection</em>') .
               $this->radio('familiy', 10, $checked, 'child') .
               $this->radio('familiy', 11, $checked, 'parent') .
               $this->radio('familiy', 12, $checked, 'sibling') .
               $this->radio('familiy', 13, $checked, 'spouse') .
               $this->radio('familiy', 14, $checked, 'kin');
    }
    
    public function physical($data) {
        $checked = $this->checked($data, array(4, 5));
        return $this->radio('physical', 0, $checked, '<em>no selection</em>') .
               $this->radio('physical', 4, $checked, 'met') .
               $this->radio('physical', 5, $checked, 'want-to-meet');
    }
    
    private function radio($name, $value, $checked, $label) {
        if($checked == $value) {
            $checked = ' checked="checked" ';
            $checked_css = ' checked';
        } else {
            $checked = '';
            $checked_css = '';
        }

        return '<input id="'.$name.$value.'" type="radio" name="data[xfn]['.$name.']" value="'.$value.'"'. $checked . '/><label for="'.$name.$value.'" class="contact_type radio'.$checked_css.'">'.$label.'</label>';
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