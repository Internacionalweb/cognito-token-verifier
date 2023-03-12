<a name="readme-top"></a>
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![AWS](https://img.shields.io/badge/AWS-232F3E?style=for-the-badge&logo=amazon-aws&logoColor=white)

<br />

<div align="center">
    <h3 align="center">Cognito Token Verifier for PHP</h3>
    <a href="https://codecov.io/github/Internacionalweb/cognito-token-verifier" > 
        <img src="https://codecov.io/github/Internacionalweb/cognito-token-verifier/branch/master/graph/badge.svg?token=LBZ4VRX3HT"/> 
    </a>
</div>

## About the Project

Cognito is a service provided by Amazon Web Services (AWS) that allows users to authenticate and access AWS resources through credentials such as ClientID and Secret, or username and password. After a user successfully authenticates, Cognito returns a JSON Web Token (JWT), which contains the main information required to verify that the user has accessed our application.

This library verifies that the signature of the JWT is valid, comes from a desired application, and that the token has not been tampered with or expired.

## Getting Started

<p align="right">(<a href="#readme-top">Go to top</a>)</p>

### Installation

1. Install the library in your project using composer

   ```
     composer require internacionalweb/cognito-token-verifier
   ```

2. Configure your project to use the library, check the [Usage examples](#usage-examples) section for more information.

### Usage examples:

- [PHP Native (No frameworks)](documents/php-native.md)
- [Symfony Custom Authenticator](documents/symfony-custom-authenticator.md)

### Contributors:

<table>
<tr>
    <td align="center" style="word-wrap: break-word; width: 120.0; height: 120.0">
        <a href=https://github.com/Pixelao>
            <img src=https://avatars.githubusercontent.com/u/8830376?v=4 width="80;"  style="border-radius:50%;align-items:center;justify-content:center;overflow:hidden;padding-top:10px" alt=Adrián Martín/>
            <br />
            <sub style="font-size:14px"><b>Adrián Martín</b></sub>
        </a>
    </td>
    <td align="center" style="word-wrap: break-word; width: 120.0; height: 120.0">
        <a href=https://github.com/seon22break>
            <img src=https://avatars.githubusercontent.com/u/36485771?v=4 width="80;"  style="border-radius:50%;align-items:center;justify-content:center;overflow:hidden;padding-top:10px" alt=Jhonatan Matías/>
            <br />
            <sub style="font-size:14px"><b>Jhonatan Matías</b></sub>
        </a>
    </td>
</tr>
</table>
