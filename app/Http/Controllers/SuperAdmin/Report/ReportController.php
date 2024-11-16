<?php

namespace App\Http\Controllers\SuperAdmin\Report;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function downloadReport(Request $request){
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $admin = Admin::where('id','!=', 1)->get();

         // Yeni bir Spreadsheet nesnesi oluştur
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bayii Raporu');

        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'ESNAF EXPRESS');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setSize(16); 
        $sheet->getStyle('A1')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('c2159d')); 
        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Bayii Satış Raporu');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setSize(12); 
        $sheet->getStyle('A2')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000')); 

        // Verileri ekle
        $sheet->setCellValue('A3', 'Bayii');
        $sheet->setCellValue('B3', 'Sipariş Sayısı');
        $sheet->getStyle('A3:B3')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(30); 
        $sheet->getColumnDimension('B')->setWidth(20);

        $writeIndex = 4;
        $totalCount = 0;
        foreach($admin as $row){
            $orderCount = Order::whereHas('restaurant', function($query) use ($row){
                return $query->where('admin_id', $row->id);
            });
            if ($request->has('start_date')){
                $orderCount->where('created_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
            }
            if ($request->has('end_date')){
                $orderCount->where('created_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
            }
            $orderCount = $orderCount->count();
            $sheet->setCellValue('A'.$writeIndex, $row->name);
            $sheet->setCellValue('B'.$writeIndex, $orderCount);
            $totalCount += $orderCount;
            $writeIndex++;
        }
        $sheet->setCellValue('A'.$writeIndex, "Toplam");
        $sheet->setCellValue('B'.$writeIndex, $totalCount); 
    
        $sheet->getStyle('A'.$writeIndex.':B'.$writeIndex)->getFont()->setBold(true);
        // Excel dosyasını indirme işlemi başlat
        $fileName = "report.xlsx";
        $response = new StreamedResponse(function() use($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Header ayarları
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="'. $fileName .'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
