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
    public function show(Request $request, $data)
    {
        $Data           = Data::where('id', $data)->first();
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');
        
        //----------------deklarasi
        $datas = Data::where('hidroponik_id', $hidroponik_id)->where('id', '<', $data)->get();

        $maxJ = $datas->max('jumlah') == null ? 0 : $datas->max('jumlah');
        $minJ = $datas->min('jumlah') == null ? 0 : $datas->min('jumlah');

        if ($datas->count('jumlah') == 0) {
            $meanJ = 0;
        }else {
            $meanJ = $datas->sum('jumlah')/$datas->count('jumlah');
        }

        $lastJ = Data::where('id', $Data->id)->first()->jumlah;
        
        $Datappm = Ppm::where('id', $ppm_id)->first()->toArray();

        $maxP = $Datappm['max'];
        $minP = $Datappm['min'];
        $meanP = ($maxP + $minP) / 2;

        $lastP    = Data::where('id', $Data->id)->first()->ppm;

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
        $z1    = (400 * $rule1) + 100;
        $rule2 = min($JTSedikit,$NPSedang); //KBaik
        $z2    = (400 * $rule2) + 100;
        $rule3 = min($JTSedikit,$NPTinggi); //KBuruk
        $z3    = 500 - (400 * $rule3);
        $rule4 = min($JTSedang,$NPRendah); //KBuruk
        $z4    = 500 - (400 * $rule4);
        $rule5 = min($JTSedang,$NPSedang); //KBaik
        $z5    = (400 * $rule5) + 100;
        $rule6 = min($JTSedang,$NPTinggi); //KBuruk
        $z6    = 500 - (400 * $rule6);
        $rule7 = min($JTBanyak,$NPRendah); //KBuruk
        $z7    = 500 - (400 * $rule7);
        $rule8 = min($JTBanyak,$NPSedang); //KBaik
        $z8    = (400 * $rule8) + 100;
        $rule9 = min($JTBanyak,$NPTinggi); //KBaik
        $z9    = (400 * $rule9) + 100;

        //-------------------- Defuzifikasi
        $pembilang = ($rule1 * $z1) + ($rule2 * $z2) + ($rule3 * $z3) + ($rule4 * $z4) + ($rule5 * $z5) + ($rule6 * $z6) + ($rule7 * $z7) + ($rule8 * $z8) + ($rule9 * $z9);
        $penyebut = $rule1 + $rule2 + $rule3 + $rule4 + $rule5 + $rule6 + $rule7 + $rule8 + $rule9;
        $z = $pembilang / $penyebut;
        
        $KBuruk = 100;
        $KBaik = 500;
        $KTengah = ($KBaik + $KBuruk) / 2;

        if ($z <= $KTengah) {
            $Kondisi = 'buruk';
        }
        elseif($z > $KTengah){
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

            'output' => $z,
            'kondisi' => $Kondisi,
        ]);
    }
}
