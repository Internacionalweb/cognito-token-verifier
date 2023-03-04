
<a name="readme-top"></a>
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![AWS](https://img.shields.io/badge/AWS-232F3E?style=for-the-badge&logo=amazon-aws&logoColor=white)

<br />

<div align="center">
    <h3 align="center">Cognito Token Verifier for PHP</h3>
</div>

## About the Project
Cognito is a service provided by Amazon Web Services (AWS) that allows users to authenticate and access AWS resources through credentials such as ClientID and Secret, or username and password. After a user successfully authenticates, Cognito returns a JSON Web Token (JWT), which contains the main information required to verify that the user has accessed our application.

This library verifies that the signature of the JWT is valid, comes from a desired application, and that the token has not been tampered with or expired.

## Getting Started

<p align="right">(<a href="#readme-top">Go to top</a>)</p>

### Instalación

1. Install the library in your project using composer
    ```
      composer require barymont/jwt-cognito-signature
    ```
2. Set up the necessary environment variables, check the [.env.example](../.env.example) file for more information.

3. Configure your project to use the library, check the [Usage examples](#usage-examples) section for more information.

### Usage examples:

- [Symfony Custom Authenticator](../documents/symfony-custom-authenticator.md)
