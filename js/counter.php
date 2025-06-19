<?php
session_start();

$counter_file = 'counter.json';

// Đọc dữ liệu cũ
$data = file_exists($counter_file) ? json_decode(file_get_contents($counter_file), true) : [
    'total' => 0,
    'today' => 0,
    'month' => 0,
    'online' => []
];

date_default_timezone_set('Asia/Ho_Chi_Minh');
$today = date('Y-m-d');
$month = date('Y-m');

// Lưu IP và thời gian truy cập
$ip = $_SERVER['REMOTE_ADDR'];
$now = time();

// Xử lý online (xóa các truy cập cũ hơn 5 phút)
$data['online'] = array_filter($data['online'], function($timestamp) use ($now) {
    return $now - $timestamp < 300;
});

// Cập nhật IP
$data['online'][$ip] = $now;

// Lưu ngày hôm nay và tháng
if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $data['total']++;

    if (!isset($data['last_day']) || $data['last_day'] != $today) {
        $data['today'] = 1;
        $data['last_day'] = $today;
    } else {
        $data['today']++;
    }

    if (!isset($data['last_month']) || $data['last_month'] != $month) {
        $data['month'] = 1;
        $data['last_month'] = $month;
    } else {
        $data['month']++;
    }
}

// Lưu lại file
file_put_contents($counter_file, json_encode($data));

// Trả về kết quả
echo json_encode([
    'online' => count($data['online']),
    'today' => $data['today'],
    'month' => $data['month'],
    'total' => $data['total']
]);
?>
