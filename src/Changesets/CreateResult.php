<?php
declare(strict_types=1);

namespace Itineris\Lottery\Changesets;

use Itineris\Lottery\Entities\Draw;
use Itineris\Lottery\Entities\Prize;
use Itineris\Lottery\Entities\Ticket;
use Itineris\Lottery\PostTypes\Result;

class CreateResult
{
    private $title;

    /**
     * The terms.
     *
     * @var \Itineris\Lottery\Entities\AbstractTerm[]
     */
    private $terms;

    public function __construct(string $title, Draw $draw, Prize $prize, Ticket $ticket)
    {
        $this->title = $title;
        $this->terms = [
            $draw,
            $prize,
            $ticket,
        ];
    }

    /**
     * Insert new result record (with draw, prize and ticket) into database.
     * Warning: This method does not check uniqueness.
     *
     * @return int The post ID on success. The value 0 or WP_Error on failure.
     */
    public function commit(): int
    {
        $record = [
            'post_title' => $this->title,
            'post_status' => 'publish',
            'post_type' => Result::POST_TYPE,
        ];

        $id = wp_insert_post($record);

        if (is_wp_error($id)) {
            wp_die($id);
        }

        foreach ($this->terms as $term) {
            wp_set_object_terms(
                $id,
                $term->getId(),
                $term::getTaxonomy()
            );
        }

        return $id;
    }
}
