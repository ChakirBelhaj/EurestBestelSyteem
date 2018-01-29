<?php

namespace App\Models;

class OrderStatusDetails {

    private $canCancel = false;
    private $canPay = false;
    private $statusString = 'Onbekend';
    private $statusLabelClass = 'default';

    public function __construct($orderStatus, $paymentStatus) {

        if ($orderStatus == Order::CANCELLED) {
            // Not paid yet
            $this->statusString = 'Geannuleerd';
            $this->statusLabelClass = 'warning';
        }
        else if ($paymentStatus == Payment::OPEN) {

            // Not paid yet
            $this->canCancel = true;
            $this->statusString = 'Nog niet betaald';
            $this->statusLabelClass = 'warning';
            $this->canPay = true;

        }
        else if ($paymentStatus == Payment::PENDING) {

            // Pending in some way
            $this->statusString = 'Betaling wordt verwerkt';
            $this->statusLabelClass = 'info';

        }
        else if ($paymentStatus == Payment::PAID || $paymentStatus == Payment::REFUNDED || $paymentStatus == Payment::IN_STORE) {

            // Payment done, check order status

            if ($orderStatus == Order::COMPLETED) {

                // Order is waiting
                $this->canCancel = true;
                $this->statusString = 'Wordt verwerkt';
                $this->statusLabelClass = 'info';

            }
            else if ($orderStatus == Order::PROCESSING) {

                // Order is being made
                $this->statusString = 'Wordt bereid';
                $this->statusLabelClass = 'success';

            }
            else if ($orderStatus == Order::FINISHED) {

                // Order is done, ready for pickup
                $this->statusString = 'Klaar';
                $this->statusLabelClass = 'success';

            }
            else if ($orderStatus == Order::CANCELLED) {

                // The order has been cancelled
                $this->statusString = 'Geannuleerd';
                $this->statusLabelClass = 'danger';

            }
        }
    }

    public function canCancel() {
        return $this->canCancel;
    }

    public function canPay() {
        return $this->canPay;
    }

    public function getStatusString() {
        return $this->statusString;
    }

    public function getStatusLabel() {
        return '<span class="label label-' . $this->statusLabelClass . '">' . $this->statusString. '</span>';
    }
}