# Change Log

## 2.1.1
  * Figured out how to use .gitattributes to exclude files from the composer package.
    * Now I can add the TA-Lib C source files to the repository and not have them show up in the composer package. 🎉
## 2.1.0
  * Added new functions from TA-Lib v0.6.0.
    * [ACCBANDS](https://github.com/TA-Lib/ta-lib/blob/main/src/ta_func/ta_ACCBANDS.c)
    * [AVGDEV](https://github.com/TA-Lib/ta-lib/blob/main/src/ta_func/ta_AVGDEV.c)
    * [IMI](https://github.com/TA-Lib/ta-lib/blob/main/src/ta_func/ta_IMI.c)
## 2.0.1
  * Fixed [Issue #16](https://github.com/LupeCode/phpTraderNative/issues/16)
  * Removed version from composer.json as per their spec.
## 2.0.0
  * Cleaned up git repository.
  * Drop support for PHP 7.X and 8.0.
  * Updated dependencies.
  * Cleaned up the code and use PSR-12 formatting.
  * Changed the LICENSE to MIT.
  * Updated PHPUnit.
  * Added better testing data.
## 1.2.3
  * Small updates and bug fixes.
  * Added .deepsource.toml for DeepSource.io.
## 1.2.2
  * GitLab CI and TravisCI no longer build this package
## 1.2.1
  * Added PHP 7.0.x to the CI tests.
    * Also various things to get this to work in the CI runners.
## 1.2.0
  * Added the static variable to the test. Use this instead of a string just in case the string for the errors change.
  * Test all of the MA types that stochrsi supports.
  * Added some test data that matches [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Added the Lupe Trader classes.
    * These classes will override certain functions that have been identified to produce bad results.
    * See [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Wrote tests with hand-done math to verify that the returned results match what it should be.
    * There were already tests that made sure the results matched the PECL Trader extension, but what if that was wrong?
    * I didn't to it by hand *per se*, but in a spreadsheet. I'm not *that* good at math.
  * Added the Slow Stoch RSI function to solve [Issue #2](https://github.com/LupeCode/phpTraderNative/issues/2).
  * Moved the common testing data into a trait for classes that cannot extend the `TraderTest` class.
  * Added the `.gitlab-ci.yml` and `.travis.yml` files.
    * Hey, who can say no to free code testing and reporting, right?
## 1.1.0
  * Removed the MyInteger class, using plain integers now.
  * Made the return message constant and easier to test against.
  * Switched to using the core classes in a static way. This will improve operating time when the same class of functions are used multiple times in one execution.
  * Added tests that cause coverage to hang with a group that can be skipped with `--exclude-group exceptions`
  * Updated the static references.
## 1.0.5
  * Added an installation notice to the README.
## 1.0.4
  * Fixed [Issue # 1](https://github.com/LupeCode/phpTraderNative/issues/1)
  * Shortened the code for htDcPeriod.
## 1.0.3
  * Added a test that shows that the PECL version and the C/JAVA versions of TA-Lib have different defaults.
  * Added testing for the TraderFriendly class.
  * Changed the version requirement of PECL Trader to 0.4.1 for require-dev.
  * Removed unused internal functions.
## 1.0.2
  * Updated for PECL Trader version 0.4.1.
  * Optimized the code.
  * Made sure that the original copyright notice was in all of the ported code.
  * Set up local tests to be in multiple versions of PHP; all versions from 7.0.x to 7.2.x.
## 1.0.1
  * Removed the old java code that was unused.
## 1.0.0
  * Lots and lots of function documentation.
  * Moved the optional parameters in to the function definitions instead of in the body of the function.
## 0.4.0
  * Finished all of the classes; the library now has the same functionality as the TA-Lib code it is ported from.
## 0.3.0
  * Set up the basic scaffolding of the library.
