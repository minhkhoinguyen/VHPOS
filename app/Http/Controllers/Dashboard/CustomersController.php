<?php

/**
 * NexoPOS Controller
 * @since  1.0
**/

namespace App\Http\Controllers\Dashboard;

use App\Crud\CouponCrud;
use App\Crud\CustomerCouponCrud;
use App\Crud\CustomerCrud;
use App\Crud\CustomerOrderCrud;
use App\Crud\CustomerRewardCrud;
use App\Models\Customer;

use Illuminate\Http\Request;
use App\Services\CustomerService;

use App\Http\Controllers\DashboardController;
use App\Models\Coupon;
use App\Models\CustomerAccountHistory;
use App\Models\CustomerReward;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Validator;

class CustomersController extends DashboardController
{
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(
        CustomerService $customerService
    )
    {
        parent::__construct();
        $this->customerService      =   $customerService;
    }

    public function createCustomer()
    {
        return CustomerCrud::form();
    }

    /**
     * Shows the list of available customers under a CRUD
     * list
     * @param void
     * @return backend vue
     */
    public function listCustomers()
    {
        return CustomerCrud::table();
    }

    /**
     * get list of avialable customers
     * @return json response
     */
    public function get( $customer_id = null )
    {
        $customer   =   Customer::with( 'group' )->find( $customer_id );

        if ( $customer instanceof Customer ) {
            return $customer;
        }

        return $this->customerService->get();
    }

    /**
     * delete a customer
     * @param int customer id
     * @return json
     */
    public function delete( $id )
    {
        return $this->customerService->delete( $id );
    }

    /**
     * create a customer using the provided
     * form request
     * @param Request
     * @return json
     * @todo implement security validation
     */
    public function post( Request $request )
    {
        $data   =   $request->only([
            'name', 'surname', 'description', 'gender', 'phone', 'email', 'pobox', 'group_id', 'address'
        ]);

        return $this->customerService->create( $data );
    }

    /**
     * edit a customer using provided
     * form request
     * @param Request form data
     * @return json
     * @todo implement a request for the validation
     */
    public function put( $customer_id, Request $request )
    {
        $data   =   $request->only([
            'name', 'surname', 'description', 'gender', 'phone', 'email', 'pobox', 'group_id', 'address'
        ]);

        return $this->customerService->update( $customer_id, $data );
    }

    /**
     * Get specific customers order
     * @param Customer entity
     * @return json
     */
    public function getOrders( $id )
    {
        return $this->customerService->get( $id )->orders->map( function( $order ) {
            switch( $order->payment_status ) {
                case Order::PAYMENT_HOLD : $order->human_status = __( 'Hold' ); break;
                case Order::PAYMENT_PAID : $order->human_status = __( 'Paid' ); break;
                case Order::PAYMENT_PARTIALLY : $order->human_status = __( 'Partially Paid' ); break;
                case Order::PAYMENT_REFUNDED : $order->human_status = __( 'Refunded' ); break;
                case Order::PAYMENT_UNPAID : $order->human_status = __( 'Unpaid' ); break;
                case Order::PAYMENT_PARTIALLY_REFUNDED : $order->human_status = __( 'Partially Refunded' ); break;
                case Order::PAYMENT_VOID : $order->human_status = __( 'Void' ); break;
                default: $order->human_status = $order->payment_status; break;
            }

            return $order;
        });
    }

    public function editCustomer( Customer $customer )
    {
        return CustomerCrud::form( $customer );
    }

    /**
     * get the address informations saved
     * under a specific customer id
     * @param int customer id
     * @return array
     */
    public function getAddresses( $id )
    {
        return $this->customerService->getCustomerAddresses( $id );
    }

    public function deleteUsingEmail( $email )
    {
        return $this->customerService->deleteUsingEmail( $email );
    }

    public function listCoupons()
    {
        return CouponCrud::table();
    }

    public function createCoupon()
    {
        return $this->view( 'pages.dashboard.coupons.create', [
            'title'         =>  __( 'Create Coupon' ),
            'description'   =>  __( 'helps you creating a coupon.' ),
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.coupons/form-config' ),
            'returnUrl'    =>  ns()->url( '/dashboard/customers/coupons' ),
            'submitMethod'  =>  'POST',
            'submitUrl'     =>  ns()->url( '/api/nexopos/v4/crud/ns.coupons' ),
        ]);
    }

    public function editCoupon( Coupon $coupon )
    {
        return $this->view( 'pages.dashboard.coupons.create', [
            'title'         =>  __( 'Edit Coupon' ),
            'description'   =>  __( 'Editing an existing coupon.' ),
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.coupons/form-config/' . $coupon->id ),
            'returnUrl'     =>  ns()->url( '/dashboard/customers/coupons' ),
            'submitMethod'  =>  'PUT',
            'submitUrl'     =>  ns()->url( '/api/nexopos/v4/crud/ns.coupons/' . $coupon->id ),
        ]);
    }

    public function searchCustomer( Request $request )
    {
        $search     =   $request->input( 'search' );
        $customers  =   Customer::with( 'billing' )
            ->with( 'shipping' )
            ->where( 'name', 'like', '%' . $search . '%' )
            ->orWhere( 'email', 'like', '%' . $search . '%' )
            ->get();

        return $customers;
    }

    public function accountTransaction( Customer $customer, Request $request )
    {
        $validation     =   Validator::make( $request->all(), [
            'operation'     =>  'required',
            'amount'        =>  'required|integer'
        ]);

        if ( $validation->fails() ) {
            throw new Exception( __( 'Invalid Request.' ) );
        }

        return $this->customerService->saveTransaction(
            $customer,
            $request->input( 'operation' ),
            $request->input( 'amount' ),
            $request->input( 'description' )
        );
    }

    public function getGroup( Customer $customer )
    {
        return $customer->group;
    }

    public function getCustomersOrders( Customer $customer )
    {
        return CustomerOrderCrud::table([
            'src'           =>  ns()->url( '/api/nexopos/v4/crud/ns.customers-orders' ),
            'queryParams'   =>  [
                'customer_id'   =>  $customer->id
            ]
        ]);
    }

    public function getCustomersRewards( Customer $customer )
    {
        return CustomerRewardCrud::table([
            'queryParams'    =>  [
                'customer_id'   =>  $customer->id
            ]
        ]);
    }

    public function editCustomerReward( Customer $customer, CustomerReward $reward )
    {
        return CustomerRewardCrud::form( $reward, [
            'returnUrl'     =>  ns()->route( 'ns.dashboard.customers-rewards', [ 'customer' => $customer->id ]),
            'queryParams'   =>  [
                'customer_id'   =>  $customer->id
            ]
        ]);
    }

    public function getCustomersCoupons( Customer $customer )
    {
        return CustomerCouponCrud::table([
            'queryParams'   =>  [
                'customer_id'   =>  $customer->id
            ]
        ]);
    }

    public function getCustomerCoupons( Customer $customer )
    {
        return $customer->coupons;
    }

    public function loadCoupons( Request $request, $code )
    {
        return $this->customerService->loadCoupon( $code, $request->input( 'customer_id' ) );
    }
}

