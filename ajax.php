<?php
require_once 'core/init.php';

// check for action ignore random calls
if (isset($_POST['action'])) {
  if ($_POST['action'] === 'searchHint') {
    if (isset($_POST['searchString'])) {
      header('Content-Type: text/plain; charset=utf-8');
      // API call based on current search string
      $results = $KSamsok->searchHint($_POST['searchString'], '3');
      if ($results['count'] !== '0') {
        for ($i=0; $i < $results['count']; $i++) {
          echo '<span onclick="bildPlats.app.searchImages(\'' . $results['hints'][$i]['value'] . '\');">' . ucfirst($results['hints'][$i]['value']) . '</span>';
        }
      } else {
        echo '';
      }
    }
  }

  if ($_POST['action'] === 'search') {
    if (isset($_POST['searchString'])) {
      $results = $KSamsok->photoSearch($_POST['searchString']);

      if (!empty($results)) {
        for ($i=0; $i < count($results); $i++) {
          if (empty($results[$i]['presentation']['coordinates']) && $db::getObject($KSamsok->uriFormat($results[$i]['presentation']['uri'], 'rawurl')) === false) {
            $results[$i]['local']['uri'] = $KSamsok->uriFormat($results[$i]['presentation']['uri'], 'raw');
            $searchResult[] = $results[$i];
          }
        }

        // output result as JSON
        if (isset($searchResult)) {
          header('Content-type: application/json');
          echo json_encode($searchResult);
        } else {
          header('Content-type: application/json');
          echo('{"result": "error","message": "Inga Foton Hittades"}');
        }
      } else {
          header('Content-type: application/json');
          echo('{"result": "error","message": "Inga Foton Hittades"}');
      }
    }
  }

  if ($_POST['action'] === 'save') {
    if (isset($_POST['uri']) && isset($_POST['location'])) {
      // try to save(pass $KSamsok connection because PHP sucks right now)
      if ($db::save($_POST['uri'], $_POST['location'], $KSamsok)) {
        // added to db :-)
        header('Content-type: application/json');
        echo('{"result": "correct","message": "Platsen Sparades"}');
      } else {
        // saving failed
        header('Content-type: application/json');
        echo('{"result": "error","message": "Något Gick Fel"}');
      }
    } else {
      header('Content-type: application/json');
        echo('{"result": "error","message": "Något Gick Fel"}');
    }
  }
}
