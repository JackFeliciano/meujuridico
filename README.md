# MeuJuridico

## What the project is

MeuJuridico serves a law office. The office holds the legislation the practice loads. The office searches the case files. The assistant answers legal questions for a citizen. The office routes the citizen to a lawyer.

The assistant reads a question and names the article that matches hardest inside that area of law. The office answers with the matching article. The office answers with a statement that it holds nothing on point when no article matches.

A lawyer takes the case. The matter moves one stage at a time. The matter carries the stages intake, assigned, filed, hearing, decided and closed.

## How to run it

The operator starts the built-in server from the repository root. The operator picks a free port for PORT.

```
php -S 127.0.0.1:8114 -t showroom showroom/index.php
```

The router reads the base path from the MEUJURIDICO_BASE environment variable and strips that base from the request path. The nginx location block serves the site under /meujuridico/. The variable takes this value:

```sh
MEUJURIDICO_BASE=/meujuridico/
```
The developer runs the suite from the repository root.

```
php tests/run.php
```

## What the routes answer

The router serves the page at /. The page shows the demo office and its questions.

The router answers /health with JSON. The JSON carries the fields ok, matters and open.

## How the layout is arranged

- autoload.php loads the classes.
- src/Home/ holds the model. The model covers the statute, the question, the matter, the office and the sample.
- src/App/ holds the router, the front controller and the HTML.
- showroom/ holds the demo entry file and the stylesheet.
- tests/ holds the hand-written runner, the assertion helper and the test file.
- deploy/ holds the systemd unit and the nginx location block.

## The state of the suite

The suite runs 24 checks. The developer starts the suite with the command below.

```
php tests/run.php
```

## Licence

The repository uses the Mozilla Public License 2.0. The licence allows a person to use the code, change it and share it. The licence allows a person to combine the covered files with code under other terms. A contributor who changes a covered file must share the source of that file under the same licence.
