<?php


namespace App\Services\Multiprocess;

//header('content-type:text/html;charset=utf-8' );

// 必须加载扩展
//if (!function_exists("pcntl_fork")) {
//    die("pcntl extention is must !");
//}
// pcntl_signal(SIGCHLD, SIG_IGN); //如果父进程不关心子进程什么时候结束,子进程结束后，内核会回收。

//如果不需要阻塞进程，而又想得到子进程的退出状态，则可以注释掉pcntl_wait($status)语句，或写成：
//pcntl_wait($status,WNOHANG); //等待子进程中断，防止子进程成为僵尸进程。
use App\Services\Tool;

class Pcntl
{
    // 总结
    // 进程是十分昂贵的资源，php 每个进程占用内存从几 M 到几百 M 不等，过多的创建进程反而会严重拖垮系统，使系统变得缓慢，甚至宕机。
    // php 进程都是独立的运行单位，进程间的数据库、Redis 连接都是独立的，要做好资源使用控制和进程间通信。
    // php 多进程编程适用于 cli 模式，对于 php-fpm 运行模式下，尽量不要采取多进程编程，会出现无法预测的问题。

    // PHP的进程控制支持实现了Unix方式的进程创建, 程序执行, 信号处理以及进程的中断。
    // 进程控制不能被应用在Web服务器环境，当其被用于Web服务环境时可能会带来意外的结果。

    // 注意两点：如果是在循环中创建子进程,那么子进程中最后要exit(),防止子进程进入循环。
    //           子进程中的打开连接不能拷贝，使用的还是主进程的，需要用多例模式。

    // PHP真正的多进程运行模式，适用于数据采集、邮件群发、数据源更新、tcp服务器等环节。

    // 还可以写一个php文件，然后在以后台形式来运行它
    //      Action代码
    //      public function createAction(){
    //          //....
    //          //将args替换成要传给insertLargeData.php的参数，参数间用空格间隔
    //          system('php -f insertLargeData.php ' . ' args ' . '&');
    //          $this->redirect('/');
    //      }
    //      然后在insertLargeData.php文件中做数据库操作。也可以用cronjob + php的方式实现大数据量的处理。

    //  如果是在终端运行php命令，当终端关闭后，刚刚执行的命令也会被强制关闭，如果你想让其不受终端关闭的影响，可以使用nohup命令实现：
    //      Action代码
    //      public function createAction(){
    //          //....
    //          //将args替换成要传给insertLargeData.php的参数，参数间用空格间隔
    //          system('nohup php -f insertLargeData.php ' . ' args ' . '&');
    //          $this->redirect('/');
    //      }
    //  你还可以使用screen命令代替nohup命令。



    // int pcntl_fork(void)函数来创建进程
    //          需要注意是，父进程和子进程都从调用pcntl_fork函数位置开始，分别向下继续执行。
    //          不同的是
    //                父进程在执行过程中，得到的pcntl_fork返回值为子进程号（PID），
    //                而子进程得到的是0，
    //                如果调用pcntl_fork创建进程失败，则会返回-1。
    //          从调用pcntl_fork函数创建进程后，父进程和子进程无论是数据空间还是指令指针都完全一致，两者再也没有任何继承关系，
    //          可以看成是两个独立的进程，通过pcntl_fork返回值，来对他们进行区分。
    // posix_getpid函数来获取当前进程的id

    // 孤儿进程：当一个父进程退出，而它的一个或多个子进程还在运行，那么这些子进程将会成为孤儿进程。
    //           也就是说孤儿进程是没有父进程的进程，不会产生什么危害，因为孤儿进程会被init进程(进程号为1)所管理，并由init进程对它们完成状态收集工作。
    //           这样的话，子进程就是可以脱离 ssh, 直接进程后台运行模式，由干爹进程 1 来管理了。利用这一点，可以开发守护进程。
    // 僵尸进程：当子进程退出，而父进程仍在运行，且没有调用wait或waitpid获取子进程的状态信息，那么子进程的进程描述符仍然保存在系统中，这类进程称之为僵死进程。
    //           僵尸进程，会一直占用有限的进程号，当系统的进程号用尽的时候，就不能创建新的进程了。

    //           因为僵尸进程的状态为defunct，在执行期间，我们可以通过下面任一一种命令查看当前的僵尸进程
    //              ps -ef | grep defunct
    //              ps aux | grep -w 'Z'

    //           通过结束父进程，可以清除僵尸进程，但是有的时候，父进程是一个守护进程，那么又怎样防止僵尸进程呢？

    //           针对这个问题，PHP的pcntl扩展提供了pcntl_waitpid 函数，来收集子进程的信息。该函数定义如下：
    //               int pcntl_waitpid ( int $pid , int &$status [, int $options = 0 ] )
    //               1.pid
    //                  < -1    等待任意进程组ID等于参数pid给定值的绝对值的进程。
    //                  -1      等待任意子进程;与pcntl_wait函数行为一致。
    //                  0       等待任意与调用进程组ID相同的子进程。
    //                  > 0     等待进程号等于参数pid值的子进程。
    //              2.status
    //                  pcntl_waitpid()将会存储状态信息到status 参数上，这个通过status参数返回的状态信息可以用以下函数
    //                          pcntl_wifexited(), pcntl_wifstopped(), pcntl_wifsignaled(), pcntl_wexitstatus(), pcntl_wtermsig()
    //                          以及 pcntl_wstopsig()获取其具体的值。
    //              3.options
    //                  如果您的操作系统（多数BSD类系统）允许使用wait3，您可以提供可选的options 参数。如果这个参数没有提供，wait将会被用作系统调用。
    //                  如果wait3不可用，提供参数 options不会有任何效果。
    //                  options的值可以是0 或者以下两个常量或两个常量“或运算”结果（即两个常量代表意义都有效）。
    //                  options可用的值：
    //                          WNOHANG,如果没有子进程退出立刻返回;
    //                          WUNTRACED,子进程已经退出并且其状态未报告时返回。
    //              返回值
    //                  pcntl_waitpid()返回退出的子进程进程号，发生错误时返回-1,如果提供了 WNOHANG作为option（wait3可用的系统）并且没有可用子进程时返回0。

    //  pcntl_exec ( string $path [, array $args [, array $envs ]] )
    //  在当前的进程空间中执行指定程序，类似于c中的exec族函数。所谓当前空间，即载入指定程序的代码覆盖掉当前进程的空间，执行完该程序进程即结束。
    //      $dir = '/home/shankka/';
    //      $cmd = 'ls';
    //      $option = '-l';
    //      $pathtobin = '/bin/ls';
    //
    //      $arg = array($cmd, $option, $dir);
    //
    //      pcntl_exec($pathtobin, $arg);
    //      echo '123';  //不会执行到该行

    //   pcntl_getpriority ([ int $pid [, int $process_identifier ]] )
    //   取得进程的优先级，即nice值，默认为0，
    //  在我的测试环境的linux中（CentOS release 5.2 (Final)），优先级为-20到19，-20为优先级最高，19为最低。（手册中为-20到20）。
    //
    //  pcntl_setpriority ( int $priority [, int $pid [, int $process_identifier ]] )
    //  设置进程的优先级。
    //
    //  posix_kill
    //  可以给进程发送信号
    //  pcntl_singal
    //  用来设置信号的回调函数

    // pcntl_wifexited(int $status) 检查子进程状态代码是否代表正常退出,
    // pcntl_wexistatus(int $status) 返回一个中断的子进程返回代码，仅在正常中断才有效
    // pcntl_wifsignaled(int $status) 检查子进程是否由某个未捕获的信号退出的。是返回true,否返回false
    // pcntl_wtermsig(int $status)返回导致子进程中断的信号，当pcntl_wifsignaled返回true时有效

    // pcntl_alarm(int $seconds):为进程设置一个alarn闹钟信号
    // pcntl_signal(int $signo, callback $handler [, bool $restart_syscalls = true ] )为指定的信号安装一个新的信号处理器
    // pcntl_signal_get_handler(int $signo) 获取指定信号的处理函数

    // getmypid() 获取当前php进程的pid
    // posix_getpid() 获取当前进程的pid

    /**
     * 异步执行任务---拆分为多个进程，同时执行，提高运行速度【指定同时最多运行进程数】
     * @param mixed $doFun 需要每一项需要执行的闭包函数  function($taskInfo){} ； 参数：$taskInfo 为 $taskArr参数中的每一项 ;--注意:这个方法不存在用引传值，因为是新的进程
     * @param array $taskArr 需要分多进程执行的任务数组 -- 二维数组，每一项就是要执行的相关数据
     * @param int $maxChildPro  同时运行的最大的子进程数量--默认 10
     * @param array $extendParams  其它扩展参数
     * [
     *   'operateNo' => 操作配置 值为【1、2、4、8...】
     *                      1、如果父进程不关心子进程什么时候结束,子进程结束后，内核会回收。--子进程可成为孤儿进程--一般不用
     *                      2、设置临时改变php内存及执行时间
     *                      4、echo 打印日志，控制台好显示
     *                      8、没有安装多进程扩展 pcntl 时 不用 执行任何操作【技术在这个方法后面去写执行】--针对不想单批次执行的
     * ]
     * @return mixed 如果 真的分多进程执行了，则返回 >0 ：最后一个子进程号； -1:fork失败; -2: $taskArr参数为空;
     *                              -3:没有安装多进程扩展 pcntl --单进程执行的；-4没有安装多进程扩展 pcntl --没有执行任何操作【技术在这个方法后面去写执行】--针对不想单批次执行的
     */
    public static function asyTask($doFun, $taskArr = [], $maxChildPro = 10, $extendParams = []){
        $pid = 0;
        if(empty($taskArr)) return -2;
//        header('content-type:text/html;charset=utf-8' );
        $operateNo = $extendParams['operateNo'] ?? 0;
        if(($operateNo & 2) == 2) Tool::phpInitSet();
        // 必须加载扩展
        if (!function_exists("pcntl_fork")) {
            // die("pcntl extention is must !");
            if(($operateNo & 4) == 4)  echo date('H:i:s') . 'pcntl extention is must !' . PHP_EOL;
            if(($operateNo & 8) == 8) return -4;
            foreach($taskArr as &$taskInfo){
                if(is_callable($doFun)){
                    $doFun($taskInfo);
                }
            }
            return -3;
        }

        if(($operateNo & 4) == 4)  echo date('H:i:s') . '下面开始创建进程了'.PHP_EOL;

//        echo date('H:i:s') .'pcntl_wait 开始' . PHP_EOL;
//        $rs = pcntl_wait($status);
//        echo date('H:i:s') .'pcntl_wait 结束:'.$rs . PHP_EOL;

        //  如果父进程不关心子进程什么时候结束，那么可以用signal(SIGCHLD, SIG_IGN)通知内核，自己对子进程的结束不感兴趣，
        //  那么子进程结束后，内核会回收，并不再给父进程发送信号
        if(($operateNo & 1) == 1)  pcntl_signal(SIGCHLD, SIG_IGN); //如果父进程不关心子进程什么时候结束,子进程结束后，内核会回收。

        $childs = [];
        $pName = '';
        foreach($taskArr as &$taskInfo){
            // usleep(1000);
           //  sleep(1);
            $pid = pcntl_fork(); // 一旦调用成功，事情就变得有些不同了
            if ($pid == -1) {//fork失败
                if(($operateNo & 4) == 4)  echo date('H:i:s') . '进程失败', PHP_EOL;
                die('fork failed');
            } else if ($pid == 0) {//子进程
                if(($operateNo & 4) == 4)  echo date('H:i:s') . '子进程进入', PHP_EOL;
                $pName = '子进程';
                if(is_callable($doFun)){
                    $doFun($taskInfo);
                }
                //得到父进程id
                //$ppid = posix_getppid(); //如果$ppid为1则表示其父进程已变为init进程，原父进程已退出
                //得到子进程id：posix_getpid()或getmypid()或是fork返回的变量$pid

                if(($operateNo & 4) == 4)  echo date('H:i:s') . $pName .'(PID:'.getmypid().')结束' . PHP_EOL;
                //结束当前子进程，以防止生成僵尸进程
                if(function_exists("posix_kill")){
                    posix_kill(getmypid(), SIGTERM);
                }else{
                    system('kill -9 '. getmypid());
                }
                exit();// 如果是在循环中创建子进程,那么子进程中最后要exit(),防止子进程进入循环。
            } else {//父进程
                if(($operateNo & 4) == 4)  echo date('H:i:s') . '父进程进入', PHP_EOL;
                $pName = '父进程';
                $childs[$pid] = 1; //收集子进程
                if(count($childs) >= $maxChildPro){
                    static::wait($childs, posix_getpid(), $operateNo);
                }
            }
        }

        $curPid = posix_getpid();// 当前进程号
        if(($operateNo & 4) == 4)  echo date('H:i:s') . $pName .':(PID:' .$curPid. ')' . PHP_EOL;

        if(($operateNo & 1) != 1){
            while(count($childs) > 0) { //搜索子进程的信息，并清除子进程
                //阻塞主进程，直到一个子进程退出或接收到一个信号要求中断当前进程或调用一个信号处理函数，才往下处理
                //这样可以防止，空转
//            $childPid = pcntl_wait($status);
//            echo date('H:i:s')  . $pName .':pcntl_wait:' . $childPid .':status:' . $status .':(PID:' .$curPid. ')' . PHP_EOL;
//
//            if ($childPid != -1) {
//                unset($childs[$childPid]);
//            }
                static::wait($childs, $curPid, $operateNo);
            }

//        这是原来的方式，后来改为上面的方式
//        while(count($childs) > 0) { //搜索子进程的信息，并清除子进程
//            //阻塞主进程，直到一个子进程退出或接收到一个信号要求中断当前进程或调用一个信号处理函数，才往下处理
//            //这样可以防止，空转
//            pcntl_wait($status);
//            foreach($childs as $t_pid => $t_v) {
//                $res = pcntl_waitpid($t_pid, $status, WNOHANG);
//
//                // If the process has already exited
//                if($res == -1 || $res > 0){
//                    echo date('H:i:s') . 'pcntl_waitpid:' . $res .':(PID:' .$curPid. ')' . PHP_EOL;
//                    echo date('H:i:s') . 'status:' . $status .':(PID:' .$curPid. ')' . PHP_EOL;
//                    unset($childs[$key]);
//                }
//            }
//        }
        }

        if(($operateNo & 4) == 4)  echo date('H:i:s') . $pName .'(PID:'.$curPid.')结束' . PHP_EOL;
        return $pid;
    }

    /**
     * 阻塞主进程，直到一个子进程退出或接收到一个信号要求中断当前进程或调用一个信号处理函数，才往下处理
     * @param array $childs 收集的子进程
     * @param int $curPid 当前进程的id
     * @param int $operateNo 操作配置 值为【1、2、4、8...】
     *                      1、如果父进程不关心子进程什么时候结束,子进程结束后，内核会回收。--子进程可成为孤儿进程
     *                      2、设置临时改变php内存及执行时间
     *                      4、echo 打印日志，控制台好显示
     * @return mixed
     */
    public static function wait(&$childs, $curPid, $operateNo = 0){
        $childPid = pcntl_wait($status);
        if(($operateNo & 4) == 4)  echo date('H:i:s')  . ':子进程pcntl_wait:' . $childPid .'执行完成:status:' . $status .':(PID:' .$curPid. ')' . PHP_EOL;

        if ($childPid != -1) {
            unset($childs[$childPid]);
        }
    }

    /**
     *
  public static function aaa(){

      $pid = pcntl_fork();
      if ($pid < 0) {// 创建失败
          throw new \RuntimeException('Unable to create savegame fork.');
      } elseif ($pid > 0) {// 父进程
          // we're the savegame now... let's wait and see what happens
          pcntl_waitpid($pid, $status);

          // worker exited cleanly, let's bail
          if (!pcntl_wexitstatus($status)) {
              posix_kill(posix_getpid(), SIGKILL);
          }

          // worker didn't exit cleanly, we'll need to have another go
          $this->createSavegame();
      }

      $i  = 0;
      $child_pids	= [];
      $is_parent  = true;
      for($i=0;$i<=10;$i++){
          $this->pid  = pcntl_fork();

          if($this->pid == -1) {
              //错误处理：创建子进程失败时返回-1.
              die('could not fork');
          } else if($this->pid) {
              $child_pids[]   = $this->pid;
              $is_parent  = true;
              if(!isset($this->redis)){
                  echo '父进程创建redis链接';
                  $this->redis	= Redis::connection('local');
              }
              $this->redis->setnx('a',10);
              $this->redis->incr('a');
              echo '父进程'.$i.'---'.$this->redis->get('a')."\n";
              //父进程会得到子进程号，所以这里是父进程执行的逻辑
              //continue;
          } else {
              $is_parent  = false;
              if(isset($this->redis)){
                  echo '子进程close redis链接';
                  $this->redis->quit();

              }
              echo '子进程创建redis链接';
              $this->redis	= Redis::connection('local');
              echo '子进程'.$i.'---'.$this->redis->get('a')."\n";
              sleep(15);
              break;
          }
      }
      $complete   = false;
      if($is_parent){
          for($i=0;$i<=100;$i++){
              $complete   = true;
              foreach($child_pids as $pid){
                  /*$tmp_status = pcntl_getpriority($pid);
                  var_dump($tmp_status);
                  $complete   = ($complete == false ? false:($tmp_status === false ? true:false));
                  $this->line($pid . ':' . ($tmp_status === false ? '完成':'进行中'));* /
                  $this->line(pcntl_waitpid(0,$status).'完成');
              }
              if($complete){
                  break;
              }
              $this->line("\n");
              sleep(5);
          }
      }
      $this->warn('完成');
      exit;
      //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      pcntl_signal(SIGCHLD, SIG_IGN);
      foreach ($serviceList as $val) {
          //$this->base_service->feiniuCaigouStatus(1084581);
          $pid = pcntl_fork();
          if ($pid == 0) {
              $this->base_service->feiniuCaigouStatus($val['mid']);
              exit(0);
          } else if ($pid) {
              pcntl_wait($status, WNOHANG);
          } else {
              die('could not fork');
          }
      }

      //--------------------------------------------

      //多进程开始
      pcntl_signal(SIGCHLD, SIG_IGN);
      foreach ($agencyInfo as $agency) {
          $pid = pcntl_fork();
          if ($pid == 0) {
              $this->cron->updatepackagestatus($agency['mid'], $today);
              exit(0);
          } else if ($pid) {
              pcntl_wait($status, WNOHANG);
          } else {
              die('could not fork');
          }
      }
    // -------------------------------------------

      $this->pid  = pcntl_fork();

      if($this->pid == -1) {
          //错误处理：创建子进程失败时返回-1.
          $error_products[]	= $row->product_id;
          $this->warn('创建子进程失败'.$row->product_id);
      } else if($this->pid) {
          $this->child_pids[$this->pid]	= $row->product_id;
          if(count($this->child_pids) >= $this->max_child){
              $this->wait();
          }

          //父进程会得到子进程号，所以这里是父进程执行的逻辑
          //continue;
      } else {

          // 解决进程资源互斥问题
          if (isset($this->redis)) {
              $this->redis->quit();
          }
          DB::disconnect('backup');
      }
      $bar->finish();$this->line("\n");

      $this->line('任务分配结束,等待子进程结束');

      $bar = $this->output->createProgressBar(count($this->child_pids));
      for($i=0;$i<count($this->child_pids);$i++){
          $pid	= pcntl_waitpid(0,$status);
          $bar->advance();
          $this->complete_product_ids[]	= $this->child_pids[$pid];
      }
      $bar->finish();$this->line("\n");

      private function wait(){
          $pid	= pcntl_waitpid(0,$status);
          //var_dump($pid);
          $this->complete_product_ids[]	= $this->child_pids[$pid];
          unset($this->child_pids[$pid]);
      }
  }

     *
     */


}
