.PHONY: test testcov

test:
	php vendor/bin/phpunit

testcov:
	php vendor/bin/phpunit --coverage-html coverage
