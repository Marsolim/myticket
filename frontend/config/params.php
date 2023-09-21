<?php
return [
    'adminEmail' => 'admin@example.com',
    'ticketStatusRegister' => [
        'common\\models\\tickets\\actions\\Open' => ['id' => 1, 'status' => 'Open', 'color' => 'bg-primary', 'description' => 'Open = Belum kunjungan'],
        'common\\models\\tickets\\actions\\Repair' => ['id' => 2, 'status' => 'Pending', 'color' => 'bg-secondary', 'description' => 'Pending = Pekerjaan belum selesai'],
        'common\\models\\tickets\\actions\\closings\\Closing' => ['id' => 3, 'status' => 'Selesai', 'color' => 'bg-success', 'description' => 'Selesai = Pekerjaan selesai'],
        'common\\models\\tickets\\actions\\closings\\Awaiting' => ['id' => 4, 'status' => 'Waiting', 'color' => 'bg-info', 'description' => 'No Problem = Tidak ada masalah'],
        'common\\models\\tickets\\actions\\closings\\NoProblem' => ['id' => 5, 'status' => 'No Problem', 'color' => 'bg-primary', 'description' => 'Duplicate = Double input AHO'],
        'common\\models\\tickets\\actions\\closings\\Duplicate' => ['id' => 6, 'status' => 'Duplicate', 'color' => 'bg-warning', 'description' => 'Waiting = Menunggu remote IT'],
        'common\\models\\tickets\\actions\\Discretion' => ['id' => 7, 'status' => 'MC <i class="fa fa-xmark"></i>', 'color' => 'bg-warning', 'description' => 'Tidak tercover MC']
    ]
];
