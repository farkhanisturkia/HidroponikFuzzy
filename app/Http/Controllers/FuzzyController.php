<?php

namespace App\Http\Controllers;

use App\Models\Ppm;
use App\Models\Data;
use Illuminate\Http\Request;

class FuzzyController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request, Data $data)
    {
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');
        
        //----------------deklarasi
        $date = Data::where('id', $data->id)->get('tanggal');
        $datas = Data::select('jumlah')
                 ->where('hidroponik_id', $hidroponik_id)
                 ->where('tanggal', '<=', $date[0]['tanggal'])
                 ->get();

        $maxJ = $datas->max('jumlah');
        $minJ = $datas->min('jumlah');

        if ($datas->count('jumlah') == 0) {
            $meanJ = "";
            $lastJ = "";
        }else {
            $meanJ = $datas->sum('jumlah')/$datas->count('jumlah');
            $lastJ = Data::select('jumlah')->where('id', $data->id)->first()->jumlah;
        }
        
        $Datappm = Ppm::where('id', $ppm_id)->first()->toArray();

        $maxP = $Datappm['max'];
        $minP = $Datappm['min'];
        $meanP = ($maxP + $minP) / 2;

        $lastP    = Data::select('ppm')->where('id', $data->id)->first()->ppm;

        //------------------fuzzifikasi
        //Jumlah Tanaman
        if ($lastJ < $minJ) {
            $JTSedikit  = 1;
            $JTSedang   = 0;
            $JTBanyak   = 0;
        }
        else if ($lastJ >= $minJ && $lastJ < $meanJ) {
            $JTSedikit  = ($meanJ - $lastJ)     /   ($meanJ - $minJ);
            $JTSedang   = ($lastJ - $minJ)    /   ($meanJ - $minJ);
            $JTBanyak   = 0;
        }
        else if ($lastJ >= $meanJ && $lastJ < $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = ($maxJ - $lastJ)    /   ($maxJ - $meanJ);
            $JTBanyak   = ($lastJ - $meanJ)    /   ($maxJ - $meanJ);
        }
        else if ($lastJ >= $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = 0;
            $JTBanyak   = 1;
        }

        //Nilai PPM
        if ($lastP < $minP) {
            $NPRendah   = 1;
            $NPSedang   = 0;
            $NPTinggi   = 0;
        }
        else if ($lastP >= $minP && $lastP < $meanP) {
            $NPRendah   = ($meanP - $lastP)     /   ($meanP - $minP);
            $NPSedang   = ($lastP - $minP)    /   ($meanP - $minP);
            $NPTinggi   = 0;
        }
        else if ($lastP >= $meanP && $lastP < $maxP) {
            $NPRendah   = 0;
            $NPSedang   = ($maxP - $lastP)    /   ($maxP - $meanP);
            $NPTinggi   = ($lastP - $meanP)    /   ($maxP - $meanP);
        }
        else if ($lastP >= $maxP) {
            $NPRendah  = 0;
            $NPSedang   = 0;
            $NPTinggi   = 1;
        }

        // -------------------- Inferensi
        // rule base
        $rule1 = min($JTSedikit,$NPRendah); //KBaik
        $rule2 = min($JTSedikit,$NPSedang); //KBaik
        $rule3 = min($JTSedikit,$NPTinggi); //KBuruk
        $rule4 = min($JTSedang,$NPRendah); //KBuruk
        $rule5 = min($JTSedang,$NPSedang); //KBaik
        $rule6 = min($JTSedang,$NPTinggi); //KBuruk
        $rule7 = min($JTBanyak,$NPRendah); //KBuruk
        $rule8 = min($JTBanyak,$NPSedang); //KBaik
        $rule9 = min($JTBanyak,$NPTinggi); //KBaik

        //-------------------- Defuzifikasi
        $KBuruk = 100;
        $KBaik = 500;
        $KTengah = ($KBaik + $KBuruk) / 2;

        $BaseKBuruk = ($rule3 * $KBuruk) + ($rule4 * $KBuruk) + ($rule6 * $KBuruk) + ($rule7 * $KBuruk);
        $BaseKBaik  = ($rule1 * $KBaik) + ($rule2 * $KBaik) + ($rule5 * $KBaik) + ($rule8 * $KBaik) + ($rule9 * $KBaik);
        $BaseKTotal = $BaseKBuruk + $BaseKBaik;

        $DEFBuruk = $rule3 + $rule4 + $rule6 + $rule7;
        $DEFBaik = $rule1 + $rule2 + $rule5 + $rule8 + $rule9;
        $DEFTotal = $DEFBaik + $DEFBuruk;

        $output = $BaseKTotal / $DEFTotal;

        if ($output <= $KTengah) {
            $Kondisi = 'buruk';
        }
        elseif($output > $KTengah){
            $Kondisi =  'baik';
        }

        return view('fuzzy.show',[
            'maxJ' => $maxJ,
            'minJ' => $minJ,
            'meanJ' => $meanJ,
            'lastJ'  => $lastJ,
            'JTSedikit' => $JTSedikit,
            'JTSedang'  => $JTSedang,
            'JTBanyak'  => $JTBanyak,

            'maxP' => $maxP,
            'minP' => $minP,
            'meanP' => $meanP,
            'lastP'  => $lastP,
            'NPRendah' => $NPRendah,
            'NPSedang' => $NPSedang,
            'NPTinggi' => $NPTinggi,

            'rule1' => $rule1,
            'rule2' => $rule2,
            'rule3' => $rule3,
            'rule4' => $rule4,
            'rule5' => $rule5,
            'rule6' => $rule6,
            'rule7' => $rule7,
            'rule8' => $rule8,
            'rule9' => $rule9,

            'output' => $output,
            'kondisi' => $Kondisi,
        ]);
    }
}
