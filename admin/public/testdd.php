<?php
//
//global $aaaObj;
//$aaaObj = new aaa();
//callback();
//
//function callback(){
//    echo 'ddd';
//    global $aaaObj;
//    $aaaObj->test();
//}
//class aaa{
//   public static function test(){
//       echo 'test';
//   }
//}

// sem key
$sem_key = ftok(__FILE__, 'b');
$sem_id = sem_get($sem_key);
// shm key
$shm_key = ftok(__FILE__, 'm');
$shm_id = shm_attach($shm_key, 1024, 0666);
const SHM_VAR = 1;
$child_pid = [];
// fork 2 child process
for ($i = 1; $i <= 100; $i++) {
    $pid = pcntl_fork();
    //其实在fork后,子进程也会继承父进程的变量与资源,
    //在子进程echo SHM_VAR就知道了
    if ($pid < 0) {
        exit();
    } else if (0 == $pid) {
        // 获取锁
        sem_acquire($sem_id);
        if (shm_has_var($shm_id, SHM_VAR)) {
            //shm_get_var第二参数必须是int型
            $counter = shm_get_var($shm_id, SHM_VAR);
            $counter += 1;
            shm_put_var($shm_id, SHM_VAR, $counter);
        } else {
            $counter = 1;
            shm_put_var($shm_id, SHM_VAR, $counter);
        }
        /*
        有人可能不明白为什么既然某个子进程获取到锁了,在if里面都设置shm_put_var,
        其实程序是这样运行:第一,fork后,假如A子进程先到达(A,B子进程到达顺序由底层某些算法决定的),A子进程去共享内存找一个SHM_VAR值,发现没有,
        就进入else{}里面shm_put_var,设置SHM_VAR为 $counter = 1.释放锁后,进程退出
        B子进程发现现在没有锁住了,我自已先加锁,查找有无SHM_VAR值,刚好发现有值,就+1,并更新SHM_VAR值了
        */
        // 释放锁，一定要记得释放，不然就一直会被阻锁死
        sem_release($sem_id);
        exit;
    } else if ($pid > 0) {
        $child_pid[] = $pid;
    }
}
while (!empty($child_pid)) {
    foreach ($child_pid as $pid_key => $pid_item) {
        $wait_result = pcntl_waitpid($pid_item, $status, WNOHANG);
        //必须判断子进程回收的状态,如果不加判断,第一次两个子进程返回都是0,直接unset后会无法进入while,导致僵尸进程
        if ($wait_result == -1 || $wait_result > 0)
            unset($child_pid[$pid_key]);
    }
}
// 休眠2秒钟，2个子进程都执行完毕了
sleep(2);
echo '最终结果' . shm_get_var($shm_id, SHM_VAR) . PHP_EOL;
// 记得删除共享内存数据，删除共享内存是有顺序的，先remove后detach，顺序反过来php可能会报错
shm_remove($shm_id);
shm_detach($shm_id);
