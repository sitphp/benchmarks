# Usage

## Getting elapsed time

To benchmark a script with with this tool, you should create a new instance of the `SitPHP\Benchmarks\Bench` class. Then simply use the `start` method at the beginning of your script to start measuring and the `stop` method at the end of your script to stop measuring :

```php
use SitPHP\Benchmarks\Bench;

// ...

$bench = new Bench();
$bench->start();
// your script to benchmark ...
$bench->stop();
```

You can then retrieve the elapsed time after it is stopped (or even during execution) :

```php
// Get elased time
$elapsed = $bench->getElapsed();
``` 


## Naming benchmarks

Benchmarks can be named for saving purpose using the `SitPHP\Benchmarks\BenchManager` class.

```php
use SitPHP\Benchmarks\BenchManager;

$bench_manager = new BenchManager();
$bench_manager->benchmark('my_bench');

//...
$bench = $bench_manager->getBenchmark('my_bench');
```

## Grouping benchmarks

You can also create groups using the dot notation : "group.bench_name"

```php
$bench_1 = $bench_manager->benchmark('group.bench_1');
$bench_2 = $bench_manager->benchmark('group.bench_2');

//...
$benches = $bench_manager->getBenchmarkByGroup('group');
foreach($benches as $bench){
    //...
}
```

## Tagging benchmarks

Benchmarks can be tagged using the `setTags` and `addTags` method.

```php
$bench_1 = $bench_manager->benchmark();
$bench_2 = $bench_manager->benchmark();

$bench_1->setTags(['tag1', 'tag2']);

$bench_2->addTags('tag1');
$bench_2->addTags(['tag2', 'tag3']);

//...
$benches = $bench_manager->getBenchmarksByTag('tag1');
```



## Taking snapshots

You can use snapshots while your benchmark is running to get benchmark information at a specific time.


```php
$bench = new Bench();
$bench->start();
//..
$snap1 = $bench->snap();
// ...
$snap2 = $bench->snap();
//...
$bench->stop();

echo $snap1->getTime();
echo $snap->getMemoryUsage();
echo $snap->getMemoryPeak();
```

Snapshots can be named so you can save them and retrieve them whenever you need.
```php
$bench->snap('my_snap');

//...
if($bench->hasSnap('my_snap'){
    $snap = $bench->getSnap('my_snap')
}
```


## Getting min/max memory usage

If you make multiple snapshots, you can get a min/max memory usage and memory peak of all snapshots.

```php
//...
$bench->snap();
//...
$bench->snap();
//...
$bench->snap();
//...

echo $bench->getMinMemoryUsage();
echo $bench->getMaxMemoryUsage();
echo $bench->getMaxMemoryPeak();
echo $bench->getMinMemoryPeak();
```
