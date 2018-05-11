<?php
declare(strict_types=1);

namespace Itineris\Lottery\Changesets;

class CreateTerm
{
    private $taxonomy;
    private $name;

    public function __construct(string $taxonomy, string $name)
    {
        $this->taxonomy = $taxonomy;
        $this->name = $name;
    }

    /**
     * Insert new term into database.
     * Warning: This method does not check uniqueness.
     *
     * @return array|\WP_Error An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
     */
    public function commit()
    {
        return wp_insert_term(
            $this->name,
            $this->taxonomy
        );
    }
}
