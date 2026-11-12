# Roadmap

The project holds the statutes, the questions, the matters and the office in src/Home/. The router and the front controller in src/App/ answer the page and the probe. The demo entry file in showroom/ shows the office at work. The suite in tests/ runs 24 checks. The deploy folder carries the systemd unit and the nginx location block.

1. The code holds no store on disk. A contributor could add a store so the office keeps its articles and matters between restarts. The work would touch src/Home/Office.php, src/Home/Statute.php and src/Home/Matter.php.

2. The office searches for the hardest match only inside the area the question names. A contributor could let the office search every area when the question names none. The work would touch src/Home/Office.php and src/Home/Question.php.

3. The matter records only its current stage. A contributor could record each stage change with its date. The work would touch src/Home/Matter.php and the HTML in src/App/.

4. The office knows a lawyer only by handle. A contributor could add accounts with passwords so a lawyer signs in and sees only that lawyer's matters. The work would touch src/App/ and src/Home/Office.php.

5. The office searches the articles. The office holds no search over the matters. A contributor could add a search that finds a matter by citizen or summary. The work would touch src/Home/Office.php and the page in src/App/.
