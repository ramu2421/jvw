<?php include __DIR__ . '/header.php'; ?>
<section class="country-selector">
  <h2>Select Your Country</h2>
  <ul>
    <li><a href="https://au.jobvisaworld.com/">Australia</a></li>
    <li><a href="https://uk.jobvisaworld.com/">United Kingdom</a></li>
    <li><a href="https://us.jobvisaworld.com/">United States</a></li>
    <li><a href="https://in.jobvisaworld.com/">India</a></li>
  </ul>
</section>
<section class="job-search">
  <form action="/jobs/index.php" method="get">
    <input type="text" name="q" placeholder="Search jobs…">
    <button type="submit">Search</button>
  </form>
</section>
<?php include __DIR__ . '/footer.php'; ?>