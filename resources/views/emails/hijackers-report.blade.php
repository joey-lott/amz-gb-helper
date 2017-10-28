<html>

  <head>
    <title>Your Amz-GB-Helper Hijackers Report</title>
  </head>

  <body>

    Copy the following and paste it in a new email to seller-performance@amazon.com. Use the subject line: "Possible fraudulent sellers."<br><br>

    @foreach($hijackers as $hijacker)
      <strong>{{$hijacker->seller_name}}</strong><br>
       https://www.amazon.com/s?marketplaceID=ATVPDKIKX0DER&merchant={{$hijacker->seller_id}}<br><br>
    @endforeach

    We believe above sellers are engaging in fraudulent activity. Please investigate.
  </body>

</html>
