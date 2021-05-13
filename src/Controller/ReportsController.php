<?php

namespace App\Controller;

use App\Document\CaseFile;
use App\Document\Person;

use App\Service\PersonFilesBuilder;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;

class ReportsController extends AbstractController
{
    /**
     * @Route("/reports", name="reports")
     */
    public function caseReports() {
        return $this->render('cases/reports.html.twig');
    } 

    /**
     * @Route("/reports/cases-over-time", name="cases_over_time", options={"expose"=true})
     * Get count of cases for each category grouped by week
     * TODO: allow for variable date range size for grouping
     */
    public function casesOverTime(DocumentManager $dm) {
        $cases = $dm->getRepository(CaseFile::class)->findAllOrderByDate();
        $casesOverTime = array();

        foreach ($cases as $case) {
            // Organize case totals in 7 day (Sun-Sat) chunks
            $caseDate = $case->getDate();
            if ($caseDate->format('w') != 6) {
                $dateStr = $caseDate->format('Y-m-d');
                $endOfWeekDate = new \DateTime(date('Y-m-d', strtotime("next Saturday $dateStr")));
            } else {
                $endOfWeekDate = $caseDate;
            }

            $eowStr = $endOfWeekDate->format('Y-m-d');
            $category = $case->getCategory();

            if (!isset($casesOverTime[$eowStr])) {
                $casesOverTime[$eowStr] = $this->makeStatsByCategoryArray();
                $casesOverTime[$eowStr]['EndOfWeekDate'] = $eowStr;
            }

            $casesOverTime[$eowStr][$category] += 1;
            $casesOverTime[$eowStr]['All'] += 1;
        }

        return $this->json(array_values($casesOverTime));
    }

    /**
     * @Route("reports/cases-by-category", name="cases_by_category", options={"expose"=true})
     */
    public function casesByCategory(DocumentManager $dm) {
        $cases = $dm->getRepository(CaseFile::class)->findAll();
        $caseTotalsByCategory = array();
        $totalCases = count($cases);

        foreach ($cases as $case) {
            $category = $case->getCategory();

            if (!isset($caseTotalsByCategory[$category])) {
                $caseTotalsByCategory[$category] = 1;
            } else {
                $caseTotalsByCategory[$category] += 1;
            }
        }

        // Transforming raw category totals to ratios of total cases
        array_walk($caseTotalsByCategory, function(&$val, $key) use ($totalCases) {
            $val = round($val / $totalCases, 2);
        });

        return $this->json($caseTotalsByCategory);
    }

    // TODO: The category list currently lives in the CaseType form builder. Should centralize this in a service.
    private function makeStatsByCategoryArray() {
        return array(
            'EndOfWeekDate' => null,
            'Shoplifting' => 0,
            'Aggressive Behavior' => 0,
            'Harrasment' => 0,
            'Workplace Injury' => 0,
            'Foodborne Illness' => 0,
            'Vehicle Accident' => 0,
            'Vandalism' => 0,
            'Medical Emergency' => 0,
            'All' => 0,
        );
    }
}
