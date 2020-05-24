<?php


namespace app\core;


class LinkSorter
{
    public $order = [];
    private $defaults = [
        'user'   => 'asc',
        'email'  => 'asc',
        'status' => 'asc',
    ];
    private $newOrder = [
        'desc' => 'asc',
        'asc'  => 'desc'
    ];

    public function __construct($field = null, $order = null)
    {
        if (array_key_exists($order, $this->newOrder) && array_key_exists($field, $this->defaults)) {
            $this->order = [$field => ['order' => $this->newOrder[$order], 'active' => 'active']];
        }
        $this->order = array_merge($this->defaults, $this->order);

        return $this->updateLinks();
    }

    private function updateLinks()
    {
        //$html = '<div class="sorter">';
        $html = '<div class="btn-group" role="group" aria-label="sorter">';
        foreach ($this->order as $field => $order) {
            $icon   = ' &DownTeeArrow;';
            $active = '';
            if (is_array($order)) {
                $active = $order['active'];
                $order  = $order['order'];
            }
            if ($order == 'desc') {
                $icon = ' &UpTeeArrow;';
            }
            $html .= '<a href="?sort=' . $field . '-' . $order . '" class="btn btn-info ' . $active . '">' . $field . '
<span class="badge badge-light">' . $icon . '</span>
</a>';
        }
        $html .= '</div>';

        return $html;
    }

    public function __toString()
    {
        return $this->updateLinks();
    }

}