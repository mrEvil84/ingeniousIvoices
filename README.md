## Invoice Structure:

The invoice should contain the following fields:
* **Invoice ID**: Auto-generated during creation.
* **Invoice Status**: Possible states include `draft,` `sending,` and `sent-to-client`.
* **Customer Name** 
* **Customer Email** 
* **Invoice Product Lines**, each with:
  * **Product Name**
  * **Quantity**: Integer, must be positive. 
  * **Unit Price**: Integer, must be positive.
  * **Total Unit Price**: Calculated as Quantity x Unit Price. 
* **Total Price**: Sum of all Total Unit Prices.

## Required Endpoints:

1. **View Invoice**: Retrieve invoice data in the format above.
2. **Create Invoice**: Initialize a new invoice.
3. **Send Invoice**: Handle the sending of an invoice.

## Functional Requirements:

### Invoice Criteria:

* An invoice can only be created in `draft` status. 
* An invoice can be created with empty product lines. 
* An invoice can only be sent if it is in `draft` status. 
* An invoice can only be marked as `sent-to-client` if its current status is `sending`. 
* To be sent, an invoice must contain product lines with both quantity and unit price as positive integers greater than **zero**.

### Invoice Sending Workflow:

* **Send an email notification** to the customer using the `NotificationFacade`. 
  * The email's subject and message may be hardcoded or customized as needed. 
  * Change the **Invoice Status** to `sending` after sending the notification.

### Delivery:

* Upon successful delivery by the Dummy notification provider:
  * The **Notification Module** triggers a `ResourceDeliveredEvent` via webhook.
  * The **Invoice Module** listens for and captures this event.
  * The **Invoice Status** is updated from `sending` to `sent-to-client`.
  * **Note**: This transition requires that the invoice is currently in the `sending` status.

## Technical Requirements:

* **Preferred Approach**: Domain-Driven Design (DDD) is preferred for this project. If you have experience with DDD, please feel free to apply this methodology. However, if you are more comfortable with another approach, you may choose an alternative structure.
* **Alternative Submission**: If you have a different, comparable project or task that showcases your skills, you may submit that instead of creating this task.
* **Unit Tests**: Core invoice logic should be unit tested. Testing the returned values from endpoints is not required.
* **Documentation**: Candidates are encouraged to document their decisions and reasoning in comments or a README file, explaining why specific implementations or structures were chosen.

## Setup Instructions:

* Start the project by running `./start.sh`.
* To access the container environment, use: `docker compose exec app bash`.

## Solution decision

* I used `entities classes src/Modules/Invoices/Entity/Invoice.php` and `src/Modules/Invoices/Entity/InvoiceProductLine.php` to avoid mocking laravel Model classes.
* Invoice Read Model uses Dtos instead Laravel Model class to pass data to controller.

## Endpoints: 

### Create invoice

#### With empty invoice product lines

```
curl --location 'http://127.0.0.1:8087/api/invoice' \
--header 'Content-Type: application/json' \
--data-raw '{
    "customer_name": "Jan Nowak",
    "customer_email": "jan.nowak@gmail.com"
}'
```

#### With invoice product lines

```
curl --location 'http://127.0.0.1:8087/api/invoice' \
--header 'Content-Type: application/json' \
--data-raw '{

    "customer_name": "Jan Nowak",
    "customer_email": "jan.nowak@gmail.com",
    "invoice_product_lines": [
        {
            "name": "Book 12",
            "price": 1200,
            "quantity": 3
        },
        {
            "name": "Book 34",
            "price": 5200,
            "quantity": 5
        }
    ]
}'
```

### Get invoice

```
curl --location 'http://127.0.0.1:8087/api/invoice/99c7127f-a5aa-42a4-adbe-ab4a65cefb7f' \
--data ''
```

### Send invoice

```
curl --location 'http://127.0.0.1:8087/api/invoice/send/99c7127f-a5aa-42a4-adbe-ab4a65cefb7f' \
--data ''
```

### Send invoice hook delivered 

```
curl --location 'http://127.0.0.1:8087/api/notification/hook/delivered/99c7127f-a5aa-42a4-adbe-ab4a65cefb7f' \
--data ''
```
