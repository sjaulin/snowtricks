[![Codacy Badge](https://app.codacy.com/project/badge/Grade/aa4d674d40fc4fbaa1ce6416ec722786)](https://www.codacy.com/gh/sjaulin/snowtricks/dashboard?utm_source=github.com&utm_medium=referral&utm_content=sjaulin/snowtricks&utm_campaign=Badge_Grade)


## Fixtures
Init app with sample data :
```
php bin/console doctrine:fixtures:load --env=dev --group=app
```

## PHPUnit

Run single test
```
symfony php bin/phpunit .\tests\Repository\UserRepositoryTest.php
```

## Test Coverage

Update report
```
symfony php bin/phpunit --coverage-html public/test-coverage
```
View Report 
https://localhost:8000/test-coverage/