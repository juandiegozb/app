<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Company;
use App\Entity\Sales;
use App\Repository\CompanyRepository;
use App\Repository\SalesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;

class CompanySalesController extends AbstractController
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @Route("/company/sales", name="app_company_sales")
     */
    public function index(): Response
    {
        return $this->render('company_sales/index.html.twig', [
            'controller_name' => 'CompanySalesController',
        ]);
    }

    /**
     * @Route("/salesByCompanies", name="dales_by_companies_data")
     */
    public function salesByCompaniesData()
    {

        $companies = $this->connection->fetchAll("SELECT * FROM company");
        $sales = $this->connection->fetchAll("SELECT * FROM sales");


        $companiesWithSales = array();

        foreach($companies as $company) {
            $totalSales     = 0;
            $totalAmount    = 0;

            foreach($sales as $sale) {
                if ($sale['company_id'] == $company['id']){
                    $totalSales++;
                    $totalAmount += $sale['amount'];
                }
            }

            $companiesWithSales[] = [
              'companyName' => $company['company_name'],
              'totalSales' => $totalSales,
              'totalAmount' => $totalAmount
            ];
        }

        return $this->render('company_sales/index.html.twig', [
            'companiesSales' => $companiesWithSales
        ]);

    }

    /**
     * @Route("/api/sales/{id}", name="sales_by_company", methods={"GET"})
     * @param $id
     * @return void
     */
    public function salesByCompany($id) {
        $sales = $this->connection->fetchAll("SELECT * FROM sales WHERE company_id = ?",[$id]);

        $totalAmount = 0;
        foreach($sales as $sale){
            $totalAmount += $sale['amount'];
        }

        return new JsonResponse([
            'total_sales' => count($sales),
            'total_amount' => $totalAmount
        ]);
    }
}
