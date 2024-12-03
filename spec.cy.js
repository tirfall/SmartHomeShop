describe('Cypress Tests for Registration and Login', () => {
  it('Should successfully register a new user', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Registratsioon').click();
    cy.url().should('include', 'register.php');
    cy.get('input[name="username"]').type('testuser');
    cy.get('input[name="password"]').type('testpassword');
    cy.get('button[type="submit"]').click();
    cy.contains('Регистрация успешна!').should('be.visible');
  });

  it('Should successfully log in an existing user', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('testuser');
    cy.get('input[name="password"]').type('testpassword');
    cy.get('button[type="submit"]').click();
  });

  it('Should successfully add a product to cart and place an order', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('testuser');
    cy.get('input[name="password"]').type('testpassword');
    cy.get('button[type="submit"]').click();
    cy.visit('http://localhost:8000/catalog.php');
    cy.get('input[name="product_id"][value="6"]').parent('form').within(() => {
      cy.get('button[type="submit"]').contains('Lisa ostukorvi').click();
    });
    cy.contains('Kasutaja info').click();
    cy.get('button[type="submit"]').contains('Esitage tellimus').click();
    cy.contains('Täname tellimuse eest!').should('be.visible');
    cy.contains('Logi välja').click();
  });

  it('Should log in as admin and access the Admin Panel', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('admin');
    cy.get('input[name="password"]').type('admin');
    cy.get('button[type="submit"]').click();
    cy.contains('Admin paneel').click();
    cy.url().should('include', 'admin.php');
    cy.contains('Admin Panel').should('be.visible');
  });

  it('Should update a user’s balance', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('admin');
    cy.get('input[name="password"]').type('admin');
    cy.get('button[type="submit"]').click();
    cy.visit('http://localhost:8000/admin.php');
    cy.get('select[name="user_id"]').select(1);
    cy.get('input[name="new_balance"]').clear().type('1000');
    cy.get('button[name="update_balance"]').click();
  });

  it('Should add a new product', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('admin');
    cy.get('input[name="password"]').type('admin');
    cy.get('button[type="submit"]').click();
    cy.visit('http://localhost:8000/admin.php');
    cy.get('input[name="product_name"]').type('Test Product');
    cy.get('input[name="product_price"]').clear().type('26');
    cy.get('button[name="add_product"]').click();
    cy.contains('Test Product').should('be.visible');
    cy.contains('26 Mündid').should('be.visible');
  });

  it('Should edit a product, then delete it', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('admin');
    cy.get('input[name="password"]').type('admin');
    cy.get('button[type="submit"]').click();
    cy.visit('http://localhost:8000/admin.php');
    cy.contains('Test Product')
        .parent('tr')
        .within(() => {
          cy.contains('Muuda').click();
        });
    cy.url().should('include', 'edit_product.php');
    cy.get('input[name="product_name"]').clear().type('Updated Product');
    cy.get('input[name="product_price"]').clear().type('32');
    cy.get('button[name="update_product"]').click();
    cy.url().should('include', 'admin.php');
    cy.contains('Updated Product').should('be.visible');
    cy.contains('32 Mündid').should('be.visible');
  });

  it('Should delete a product', () => {
    cy.visit('http://localhost:8000/index.php');
    cy.contains('Logi sisse').click();
    cy.url().should('include', 'login.php');
    cy.get('input[name="username"]').type('admin');
    cy.get('input[name="password"]').type('admin');
    cy.get('button[type="submit"]').click();
    cy.visit('http://localhost:8000/admin.php');
    cy.contains('Updated Product')
        .parent('tr')
        .within(() => {
          cy.contains('Kustuta').click();
        });
    cy.on('window:confirm', () => true);
    cy.contains('Updated Product').should('not.exist');
  });
});
