<?php
declare(strict_types=1);

namespace Itineris\Lottery\Repositories;

use Itineris\Lottery\Changesets\CreateResult;
use Itineris\Lottery\Entities\AbstractTerm;
use Itineris\Lottery\Entities\Result;
use Itineris\Lottery\PostTypes\Result as ResultPostType;
use WP_Post;
use WP_Query;

class ResultRepo
{
    public const TITLE_SEPARATOR = '|-|';

    private $drawRepo;
    private $prizeRepo;
    private $ticketRepo;
    private $winnerRepo;

    public function __construct(
        DrawRepo $drawRepo,
        PrizeRepo $prizeRepo,
        TicketRepo $ticketRepo,
        WinnerRepo $winnerRepo
    ) {
        $this->drawRepo = $drawRepo;
        $this->prizeRepo = $prizeRepo;
        $this->ticketRepo = $ticketRepo;
        $this->winnerRepo = $winnerRepo;
    }

    public function findOrCreate(string $drawName, string $prizeName, string $ticketName, string $winnerName): Result
    {
        $title = $this->titleFor($drawName, $prizeName, $ticketName, $winnerName);

        [
            'draw' => $draw,
            'prize' => $prize,
            'ticket' => $ticket,
            'winner' => $winner,
        ] = $this->findOrCreateTermsByTitle($title);

        $results = $this->whereTerms($draw, $prize, $ticket, $winner);

        if (! empty($results)) {
            return $results[0];
        }

        $createResult = new CreateResult($title, $draw, $prize, $ticket, $winner);
        $id = $createResult->commit();

        return new Result($id, $draw, $prize, $ticket, $winner);
    }

    private function titleFor(string $drawName, string $prizeName, string $ticketName, string $winnerName): string
    {
        return sprintf(
            '%1$s' . self::TITLE_SEPARATOR . '%2$s' . self::TITLE_SEPARATOR . '%3$s' . self::TITLE_SEPARATOR . '%4$s',
            $drawName,
            $prizeName,
            $ticketName,
            $winnerName
        );
    }

    private function findOrCreateTermsByTitle(string $title): array
    {
        [$drawName, $prizeName, $ticketName, $winnerName] = explode(self::TITLE_SEPARATOR, $title);

        return [
            'draw' => $this->drawRepo->findOrCreateByName($drawName),
            'prize' => $this->prizeRepo->findOrCreateByName($prizeName),
            'ticket' => $this->ticketRepo->findOrCreateByName($ticketName),
            'winner' => $this->winnerRepo->findOrCreateByName($winnerName),
        ];
    }

    /**
     * Side effect: Create terms for existing result if those terms do not exist.
     *
     * @param AbstractTerm ...$terms Terms to be searched.
     *
     * @return Result[]
     */
    public function whereTerms(AbstractTerm ...$terms): array
    {
        $taxQuery = array_map(function (AbstractTerm $term): array {
            return [
                'taxonomy' => $term::getTaxonomy(),
                'terms' => $term->getId(),
            ];
        }, $terms);

        $rawResults = [];
        $paged = 1;

        do {
            $query = new WP_Query([
                'post_type' => ResultPostType::POST_TYPE,
                'posts_per_page' => 100,
                'paged' => $paged,
                // Fast because ids are given.
                'tax_query' => $taxQuery, // phpcs:disable WordPress.VIP.SlowDBQuery.slow_db_query_tax_query
            ]);
            $wpPosts = $query->get_posts();

            $rawResults = array_merge($rawResults, $wpPosts);

            $paged++;
        } while (! empty($wpPosts));

        return array_map(function (WP_Post $wpPost): Result {
            [
                'draw' => $draw,
                'prize' => $prize,
                'ticket' => $ticket,
                'winner' => $winner,
            ] = $this->findOrCreateTermsByTitle($wpPost->post_title);

            return new Result($wpPost->ID, $draw, $prize, $ticket, $winner);
        }, $rawResults);
    }
}
