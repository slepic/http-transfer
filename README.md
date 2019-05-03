[![Build Status](https://travis-ci.org/slepic/http-transfer.svg?branch=master)](https://travis-ci.org/slepic/http-transfer)
[![Style Status](https://styleci.io/repos/184416277/shield)](https://styleci.io/repos/184416277)

# http-transfer
Simple PHP library working with PSR HTTP message transfers.

## Usage

There are 3 components at this moment:

### Log

This component consists of:
* interface [```Slepic\Http\Transfer\Log\LogInterface```](https://github.com/slepic/http-transfer/blob/master/src/Log/LogInterface.php) defining a simple structure holing the request, response, error, time of start and end of the transfer.
  * and its implementation [```Slepic\Http\Transfer\Log\Log```](https://github.com/slepic/http-transfer/blob/master/src/Log/Log.php)
* interface [```Slepic\Http\Transfer\Log\StorageInterface```](https://github.com/slepic/http-transfer/blob/master/src/Log/StorageInterface.php) which is used to store the logs.
  * and of course its simple implementation [```Slepic\Http\Transfer\Log\ArrayStorage```](https://github.com/slepic/http-transfer/blob/master/src/Log/ArrayStorage.php) which stores the logs in array.

```
$storage = new ArrayStorage();
$storage->store(new Log($request, $start, $end, $response, $exception, $context));
assert($storage[0]->getRequest() === $request);
assert($storage[0]->getResponse() === $response);
assert($storage[0]->getException() === $exception);
assert($storage[0]->getStartTime() === $start);
assert($storage[0]->getEndTime() === $end);
assert($storage[0]->getContext() === $context);
```

### Observer

An abstraction over the transfer process where the observer is notified about start and end of transfer processes.

The observation is provided by the [```Slepic\Http\Transfer\Observer\ObserverInterface```](https://github.com/slepic/http-transfer/blob/master/src/Observer/ObserverInterface.php) which is in fact just a factory.

The observer takes the initiating request and returns a one use object implementing [```Slepic\Http\Transfer\Observer\ObserverDelegateInterface```](https://github.com/slepic/http-transfer/blob/master/src/Observer/ObserverDelegateInterface.php).

The delegate is destined to be notified of either success or failure of the transfer, and then it gets destroyed by garbage collector.

```
$observer = new SomeImplementationOfObserverInterface();
$delegate = $observer->observe($request, $context);

//process the $request
//...
//got $response ...

$delegate->success($response);

//or maybe got network exception
$delegate->error($exception);

//or maybe some client like guzzle throw exceptions for HTTP 4xx and 5xx when the response object exists along the exception
$delegate->error($exception, $exception instanceof GuzzleException ? $exception->getResponse() : null);
```

### History

A coposition of the two above that implements the osbserver to create the logs with duration information.

This one contains just an implementation of the [```Slepic\Http\Transfer\Observer\ObserverInterface```](https://github.com/slepic/http-transfer/blob/master/src/Observer/ObserverInterface.php).

The [```Slepic\Http\Transfer\History\HistoryObserver```](https://github.com/slepic/http-transfer/blob/master/src/History/HistoryObserver.php) pushes transfer logs to a storage as they get completed.

Well of course the job is actualy done by its delegate [```Slepic\Http\Transfer\History\HistoryObserverDelegate```](https://github.com/slepic/http-transfer/blob/master/src/History/HistoryObserverDelegate.php)


```
//create services
$storage = new ArrayStorage();
$observer = new HistoryObserver($storage);


//somewhere you send some requests
foreach ($requests as $request) {his 
  $delegate = $observer->observe($request);
  try {
    $response = $client->sendRequest($request);
  } catch (\Exception $e) {
    $delegate->error($e);
    throw $e;
  }
  $delegate->success($response);
}


//and when u need it you can access transfer logs
foreach ($storage as $log) {
  var_dump($log->getRequest());
  var_dump($log->getResponse());
  var_dump($log->getEndTime() - $log->getStartTime());
  //...
}
```

## Packagist Providers

* [```slepic/http-transfer-log-consumer```](https://packagist.org/providers/slepic/http-transfer-log-consumer) - consumers of LogInterface
* [```slepic/http-transfer-observer-consumer```](https://packagist.org/providers/slepic/http-transfer-observer-consumer) - consumers of ObserverInterface and ObserverDelegateInterface
* [```slepic/http-transfer-observer-implementation```](https://packagist.org/providers/slepic/http-transfer-observer-implementation) - implementations of ObserverInterface and ObserverDelegateInterface.

If you use this library in a public project and you use composer and share the project on packagist, consider adding the packagist providers above to your composer.json like this:

```
"provide": {
  "slepic/http-transfer-*-consumer": "*", //if you created a consumer of correspoding interface(s)
  "slepic/http-transfer-*-implementation": "*" //if you created implementation of corresponding interface(s)
},
"suggest": {
  "slepic/http-transfer-*-consumer": "*",  //if you created implementation of corresponding interface(s)
  "slepic/http-transfer-*-implementation": "*" //if you created a consumer of correspoding interface(s)
}
```
