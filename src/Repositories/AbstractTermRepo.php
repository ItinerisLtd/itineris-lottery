<?php
declare(strict_types=1);

namespace Itineris\Lottery\Repositories;

use Itineris\Lottery\Changesets\CreateTerm;
use Itineris\Lottery\Entities\AbstractTerm;
use Itineris\Lottery\Entities\TermFactory;
use WP_Term;

abstract class AbstractTermRepo
{
    public function findOrCreateByName(string $name): AbstractTerm
    {
        $term = $this->findByName($name);
        if (null !== $term) {
            return $term;
        }

        $createTerm = new CreateTerm(
            $this->getTaxonomy(),
            $name
        );

        $commitResult = $createTerm->commit();

        if (is_wp_error($commitResult)) {
            wp_die($commitResult);
        }

        return TermFactory::make(
            $this->getTaxonomy(),
            (int) $commitResult['term_id'],
            $name
        );
    }

    public function findByName(string $name): ?AbstractTerm
    {
        $wpTerm = get_term_by(
            'name',
            $name,
            $this->getTaxonomy()
        );

        if (false === $wpTerm) {
            return null;
        }

        return TermFactory::makeByWpTerm($wpTerm);
    }

    abstract protected function getTaxonomy(): string;

    /**
     * Get all non-empty terms.
     *
     * @return AbstractTerm[]
     */
    public function all(): array
    {
        $wpTerms = get_terms(
            $this->getTaxonomy()
        );

        if (is_wp_error($wpTerms)) {
            wp_die($wpTerms);
        }

        return array_map(function (WP_Term $wpTerm): AbstractTerm {
            return TermFactory::makeByWpTerm($wpTerm);
        }, $wpTerms);
    }
}
