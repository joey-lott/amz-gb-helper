<html>

  <head>
    <title>Your Amz-GB-Helper Multiple Offers Report</title>
  </head>

  <body>

    <div>The following listings have multiple offers.</div>

    <div>
      <ul>
    @foreach($items as $item)
        <li>
          <a href='https://www.amazon.com/gp/offer-listing/{{$item->asin}}'>https://www.amazon.com/gp/offer-listing/{{$item->asin}}</a><br>
        </li>
    @endforeach
      </ul>
    </div>
  </body>

</html>
