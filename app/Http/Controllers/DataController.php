<?php

namespace App\Http\Controllers;

use App\Models\Ppm;
use App\Models\Data;
use App\Tables\Datas;
use App\Models\Hidroponik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\Splade\SpladeTable;
use ProtoneMedia\Splade\Facades\Toast;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Hidroponik $hidroponik)
    {
        $hidroponik_id  = $hidroponik->id;
        $ppm_id         = $request->input('ppm_id');
        
        $datas = Data::with('hidroponik')->where('hidroponik_id', $hidroponik->id)->get();

        return view('data.index', [
            'datas' => SpladeTable::for($datas)
                ->column('tanggal', sortable:true)
                ->column('jumlah', label:"Jumlah Tanaman")
                ->column('volume', label:"Volume Air (Liter)")
                ->column('larutan', label:"Larutan AB Mix (MiliLiter)")
                ->column('ppm')
                ->column('kondisi')
                ->column('actions', exportAs: false)
                ->selectFilter('kondisi',[
                    'baik' => 'Baik',
                    'buruk' => 'Buruk',
                ], 
                noFilterOption: true,
                noFilterOptionLabel: 'Semua')
                ->defaultSort('tanggal', 'asc'),
            'hidroponik_id' => $hidroponik_id,
            'ppm_id'        => $ppm_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');

        return view('data.create',[
            'hidroponik_id' => $hidroponik_id,
            'ppm_id'        => $ppm_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');
        
        $larutan = $request->larutan * 1000; //Mengubah MiliLiter menjadi MiliGram
        $ppm = $larutan / $request->volume;  //Menghitung PPM

        //-------------------deklarasi fuzzy
        $datas = Data::select('jumlah')->where('hidroponik_id', $hidroponik_id)->get();
        $maxJ = $datas->max('jumlah') == null ? intval($request->jumlah) : $datas->max('jumlah');
        $minJ = $datas->min('jumlah') == null ? intval($request->jumlah) : $datas->min('jumlah');
        if ($datas->count('jumlah') == 0) {
            $meanJ = intval($request->jumlah);
        }else {
            $meanJ = $datas->sum('jumlah')/$datas->count('jumlah');
        }

        $Datappm = Ppm::where('id', $ppm_id)->first()->toArray();

        $maxP = $Datappm['max'];
        $minP = $Datappm['min'];
        $meanP = ($maxP + $minP) / 2;

        //-------------------fuzzifikasi
        //Jumlah tanaman
        if ($request->jumlah <= $minJ) {
            $JTSedikit  = 1;
            $JTSedang   = 0;
            $JTBanyak   = 0;
        }
        else if ($request->jumlah >= $minJ && $request->jumlah < $meanJ) {
            $JTSedikit  = ($meanJ - $request->jumlah)     /   ($meanJ - $minJ);
            $JTSedang   = ($request->jumlah - $minJ)    /   ($meanJ - $minJ);
            $JTBanyak   = 0;
        }
        else if ($request->jumlah >= $meanJ && $request->jumlah < $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = ($maxJ - $request->jumlah)    /   ($maxJ - $meanJ);
            $JTBanyak   = ($request->jumlah - $meanJ)    /   ($maxJ - $meanJ);
        }
        else if ($request->jumlah >= $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = 0;
            $JTBanyak   = 1;
        }
        //Nilai PPM
        if ($ppm < $minP) {
            $NPRendah   = 1;
            $NPSedang   = 0;
            $NPTinggi   = 0;
        }
        else if ($ppm >= $minP && $ppm < $meanP) {
            $NPRendah   = ($meanP - $ppm)     /   ($meanP - $minP);
            $NPSedang   = ($ppm - $minP)    /   ($meanP - $minP);
            $NPTinggi   = 0;
        }
        else if ($ppm >= $meanP && $ppm < $maxP) {
            $NPRendah   = 0;
            $NPSedang   = ($maxP - $ppm)    /   ($maxP - $meanP);
            $NPTinggi   = ($ppm - $meanP)    /   ($maxP - $meanP);
        }
        else if ($ppm >= $maxP) {
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

        if ($datas->count('kondisi') == 0) {
            if ($ppm >= $minP && $ppm <= $maxP) {
                $Kondisi = 'baik';
            } 
            else {
                $Kondisi = 'buruk';
            }
        }
        else {
            if ($z <= $KTengah) {
                $Kondisi = 'buruk';
            }
            elseif($z > $KTengah){
                $Kondisi =  'baik';
            }
        }

        Data::create([
            'hidroponik_id' => $hidroponik_id,
            'tanggal'       => $request->tanggal,
            'jumlah'        => $request->jumlah,
            'volume'        => $request->volume,
            'larutan'       => $request->larutan,
            'ppm'           => $ppm,
            'kondisi'       => $Kondisi,
        ]);

        Toast::title('Data Hidroponik Telah Ditambah')->autoDismiss(3);

        return to_route('data.index', [$hidroponik_id, 'ppm_id' => $ppm_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Data $data)
    {
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');

        return view('data.edit',[
            'datas'         => $data,
            'hidroponik_id' => $hidroponik_id,
            'ppm_id'        => $ppm_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $data)
    {
        $hidroponik_id  = $request->input('hidroponik_id');
        $ppm_id         = $request->input('ppm_id');

        $larutan = $request->larutan * 1000; //Mengubah MiliLiter menjadi MiliGram
        $ppm = $larutan / $request->volume; //Menghitung PPM

        //-------------------deklarasi fuzzy
        $datas = Data::where('hidroponik_id', $hidroponik_id)->where('id', '!=', $data)->get();
        $maxJ = $datas->max('jumlah') == null ? intval($request->jumlah) : $datas->max('jumlah');
        $minJ = $datas->min('jumlah') == null ? intval($request->jumlah) : $datas->min('jumlah');
        if ($datas->count('jumlah') == 0) {
            $meanJ = intval($request->jumlah);
        }else {
            $meanJ = $datas->sum('jumlah')/$datas->count('jumlah');
        }

        $Datappm = Ppm::where('id', $ppm_id)->first()->toArray();

        $maxP = $Datappm['max'];
        $minP = $Datappm['min'];
        $meanP = ($maxP + $minP) / 2;

        //-------------------fuzzifikasi
        //Jumlah tanaman
        if ($request->jumlah < $minJ) {
            $JTSedikit  = 1;
            $JTSedang   = 0;
            $JTBanyak   = 0;
        }
        else if ($request->jumlah >= $minJ && $request->jumlah < $meanJ) {
            $JTSedikit  = ($meanJ - $request->jumlah)     /   ($meanJ - $minJ);
            $JTSedang   = ($request->jumlah - $minJ)    /   ($meanJ - $minJ);
            $JTBanyak   = 0;
        }
        else if ($request->jumlah >= $meanJ && $request->jumlah < $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = ($maxJ - $request->jumlah)    /   ($maxJ - $meanJ);
            $JTBanyak   = ($request->jumlah - $meanJ)    /   ($maxJ - $meanJ);
        }
        else if ($request->jumlah >= $maxJ) {
            $JTSedikit  = 0;
            $JTSedang   = 0;
            $JTBanyak   = 1;
        }
        //Nilai PPM
        if ($ppm < $minP) {
            $NPRendah   = 1;
            $NPSedang   = 0;
            $NPTinggi   = 0;
        }
        else if ($ppm >= $minP && $ppm < $meanP) {
            $NPRendah   = ($meanP - $ppm)     /   ($meanP - $minP);
            $NPSedang   = ($ppm - $minP)    /   ($meanP - $minP);
            $NPTinggi   = 0;
        }
        else if ($ppm >= $meanP && $ppm < $maxP) {
            $NPRendah   = 0;
            $NPSedang   = ($maxP - $ppm)    /   ($maxP - $meanP);
            $NPTinggi   = ($ppm - $meanP)    /   ($maxP - $meanP);
        }
        else if ($ppm >= $maxP) {
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

        if ($datas->count('kondisi') == 0) {
            if ($ppm >= $minP && $ppm <= $maxP) {
                $Kondisi = 'baik';
            } 
            else {
                $Kondisi = 'buruk';
            }
        }
        else {
            if ($z <= $KTengah) {
                $Kondisi = 'buruk';
            }
            elseif($z > $KTengah){
                $Kondisi =  'baik';
            }
        }

        $Data = Data::where('id', $data)->first();
        $Data->update([
            'hidroponik_id' => $request->hidroponik_id,
            'tanggal'       => $request->tanggal,
            'jumlah'        => $request->jumlah,
            'volume'        => $request->volume,
            'larutan'       => $request->larutan,
            'ppm'           => $ppm,
            'kondisi'       => $Kondisi,
        ]);

        Toast::title('Data Hidroponik Telah Diupdate')->warning()->autoDismiss(3);

        return to_route('data.index', $request->hidroponik_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Data $data)
    {
        $data->delete();

        Toast::title('Data Hidroponik Telah Dihapus')->danger()->autoDismiss(3);

        return redirect()->back();
    }
}
