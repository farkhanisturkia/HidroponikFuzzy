<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Hidroponik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="d-flex justify-between flex-wrap gap-5 mb-4">
                        <table style="border-collapse: collapse; width: 47%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="2"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Deklarasi Fuzzy (Jumlah Tanaman)
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Max Jumlah Tanaman
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $maxJ }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Min Jumlah Tanaman
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $minJ }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rata-rata Jumlah Tanaman
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $meanJ }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="border-collapse: collapse; width: 47%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="2"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Deklarasi Fuzzy (Nilai PPM)
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Max Nilai PPM
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $maxP }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Min Nilai PPM 
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $minP }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rata-rata Nilai PPM
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $meanP }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-between flex-wrap gap-5 mb-4">
                        <table style="border-collapse: collapse; width: 47%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="2"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzifikasi (Jumlah Tanaman)
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Jumlah input
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $lastJ }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Jumlah Sedikit
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $JTSedikit }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Jumlah Sedang
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $JTSedang }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Jumlah Banyak
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $JTBanyak }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="border-collapse: collapse; width: 47%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="2"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzifikasi (Nilai PPM)
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Nilai PPM input
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $lastP }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Nilai PPM Rendah
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $NPRendah }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Nilai PPM Sedang
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $NPSedang }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Fuzzy Nilai PPM Tinggi
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $NPTinggi }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-center mb-4">
                        <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="4"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Inferensi Rule Base (9 Rule)
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:25%; width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 1
                                    </th>
                                    <td style="width:25%; width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule1 }}
                                    </td>
                                    <th
                                        style="width:25%; width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 6
                                    </th>
                                    <td style="width:25%; width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule6 }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 2
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule2 }}
                                    </td>
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 7
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule7 }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 3
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule3 }}
                                    </td>
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 8
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule8 }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 4
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule4 }}
                                    </td>
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 9
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule9 }}
                                    </td>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Rule 5
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $rule5 }}
                                    </td>
                                    <th
                                        style="width:25%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                    </th>
                                    <td style="width:25%; border: 1px solid black; padding: 8px; text-align: left;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-center">
                        <table style="border-collapse: collapse; width: 47%;">
                            <tbody>
                                <tr class="header-column">
                                    <th colspan="2"
                                        style="border: 1px solid black; padding: 8px; text-align: center; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Defuzzifikasi
                                    </th>
                                </tr>
                                <tr class="header-column">
                                    <th
                                        style="width:70%; border: 1px solid black; padding: 8px; text-align: left; background-color: #f2f2f2; position: sticky; left: 0; z-index: 1;">
                                        Kondisi Tanaman
                                    </th>
                                    <td style="width:30%; border: 1px solid black; padding: 8px; text-align: left;">
                                        {{ $kondisi }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
